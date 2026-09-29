<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\DailyDigest;
use App\Models\Error as AppError;
use App\Models\Form;
use App\Models\GeneralSetting;
use App\Models\IFEReport;
use App\Models\IfeArea;
use App\Models\Leads;
use App\Models\Order;
use App\Models\OutletRecommendation;
use App\Models\Product;
use App\Models\Tasks;
use App\Models\User;
use App\Services\Ai\ClaudeClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * The figures behind the admin Overview tab. It answers the admin's
 * question: is Ronda set up correctly, are people using it, and is anything
 * broken? The team dashboard (DashboardService) answers the manager's
 * question instead. This reuses its summary for the headline numbers.
 *
 * Everything here is about setup and adoption. Nothing reports where anyone
 * is: Ronda records a location only on a Location Stamp.
 */
class AdminDashboardService
{
    /** A rep who has not signed in for this many days needs a look. */
    public const QUIET_DAYS = 7;

    /** The attention list shows at most this many items, most urgent first. */
    public const ATTENTION_LIMIT = 5;

    /** The people card lists this many rows; "View all people" has the rest. */
    public const PEOPLE_ROWS = 8;

    /** Data health: at or above these shares a gap reads "Good" / "Fair". */
    public const GOOD_SHARE = 90;
    public const FAIR_SHARE = 60;

    private const LEVEL_ORDER = ['critical' => 0, 'warning' => 1, 'info' => 2];

    public function __construct(
        private DashboardService $dashboard,
        private FormApprovalService $approvals,
        private ClaudeClient $claude,
    ) {
    }

    public function overview(User $admin, ?Carbon $now = null): array
    {
        $now     = $now ? $now->copy() : Carbon::now();
        $summary = $this->dashboard->summary(null, $now);
        $people  = $this->people($now);
        $health  = $this->dataHealth();
        $status  = $this->systemStatus($now);

        return [
            'generated_at' => $now,
            'attention'    => $this->attention($admin, $people, $health, $status, $now),
            'status'       => $status,
            'pulse'        => $this->pulse($summary, $people, $now),
            'people'       => $people,
            'health'       => $health,
            'coverage'     => $this->coverage($now),
            'setup'        => $this->setup($people),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Needs your attention
    |--------------------------------------------------------------------------
    */

    /** Only items with something to do, most urgent first, capped at ATTENTION_LIMIT. */
    private function attention(User $admin, array $people, array $health, array $status, Carbon $now): array
    {
        $items = [];

        // Scheduled jobs that failed outright.
        foreach ($status['lines'] as $line) {
            if ($line['level'] === 'critical' && !empty($line['alert'])) {
                $items[] = [
                    'level'  => 'critical',
                    'icon'   => 'error',
                    'title'  => $line['alert'],
                    'detail' => $line['detail'],
                    'action' => 'See system status',
                    'url'    => '#adm-status',
                ];
            }
        }

        $dayAgo = $now->copy()->subDay();
        $errors = AppError::where('created_at', '>=', $dayAgo)->count();
        if ($errors > 0) {
            $screen = AppError::where('created_at', '>=', $dayAgo)
                ->whereNotNull('page_name')
                ->selectRaw('page_name, count(*) as total')
                ->groupBy('page_name')
                ->orderByDesc('total')
                ->value('page_name');

            $items[] = [
                'level'  => 'critical',
                'icon'   => 'error',
                'title'  => $this->count($errors, 'app error', 'app errors') . ' reported in the last 24 hours',
                'detail' => $screen ? 'Most came from the "' . $screen . '" screen.' : 'Reported by the mobile app.',
                'action' => 'View errors',
                'url'    => route('admin.app-errors', ['since' => '24h']),
            ];
        }

        $waiting = $this->approvals->pendingFor($admin);
        if ($waiting->isNotEmpty()) {
            $oldest = $waiting->sortBy('created_at')->first()->created_at;

            $items[] = [
                'level'  => 'warning',
                'icon'   => 'form',
                'title'  => $waiting->count() === 1
                    ? '1 form is waiting for your approval'
                    : $waiting->count() . ' forms are waiting for your approval',
                'detail' => $waiting->count() === 1
                    ? 'It was sent ' . $this->daysAgo($oldest, $now) . '.'
                    : 'The oldest was sent ' . $this->daysAgo($oldest, $now) . '.',
                'action' => 'Review forms',
                'url'    => route('form.tasks'),
            ];
        }

        $quiet = collect($people['rows'])->where('quiet', true);
        if ($quiet->isNotEmpty()) {
            $items[] = [
                'level'  => 'warning',
                'icon'   => 'clock',
                'title'  => $quiet->count() === 1
                    ? '1 rep has not signed in for ' . self::QUIET_DAYS . ' days'
                    : $quiet->count() . ' reps have not signed in for ' . self::QUIET_DAYS . ' days',
                'detail' => $this->nameList($quiet->pluck('name')) . '.',
                'action' => 'View people',
                'url'    => route('users.index', ['focus' => 'quiet']),
            ];
        }

        $noApp = collect($people['rows'])->where('no_app', true)->count();
        if ($noApp > 0) {
            $items[] = [
                'level'  => 'warning',
                'icon'   => 'bell-off',
                'title'  => $noApp === 1
                    ? '1 person cannot get app notifications'
                    : $noApp . ' people cannot get app notifications',
                'detail' => 'They have not signed in to the Ronda app on a phone, or notifications are off.',
                'action' => 'View people',
                'url'    => route('users.index', ['focus' => 'no_app']),
            ];
        }

        $unassigned = $health['unassigned'];
        if ($unassigned > 0) {
            $items[] = [
                'level'  => 'info',
                'icon'   => 'store',
                'title'  => $unassigned === 1
                    ? '1 outlet has no rep assigned'
                    : $unassigned . ' outlets have no rep assigned',
                'detail' => $this->unassignedByArea() . '.',
                'action' => 'Assign reps',
                'url'    => route('lead.index', ['gap' => 'unassigned']),
            ];
        }

        // Stable: items of the same level keep the order they were added in.
        $sorted = collect($items)
            ->sortBy(fn ($item, $index) => [self::LEVEL_ORDER[$item['level']], $index])
            ->values();

        return [
            'total' => $sorted->count(),
            'items' => $sorted->take(self::ATTENTION_LIMIT)->all(),
            'more'  => max(0, $sorted->count() - self::ATTENTION_LIMIT),
        ];
    }

    /** "4 in Petaling Jaya and 2 in Puchong", or "... and 3 elsewhere". */
    private function unassignedByArea(): string
    {
        $areas = IfeArea::pluck('area', 'id');
        $rows  = Leads::whereNull('assign_to')
            ->selectRaw('ife_area_id, count(*) as total')
            ->groupBy('ife_area_id')
            ->orderByDesc('total')
            ->get();

        $parts = $rows->take(2)->map(fn ($row) => $row->total . ' ' . ($row->ife_area_id && isset($areas[$row->ife_area_id])
            ? 'in ' . $areas[$row->ife_area_id]
            : 'with no area'))->all();

        $rest = (int) $rows->slice(2)->sum('total');
        if ($rest > 0) {
            $parts[] = $rest . ' elsewhere';
        }

        return $this->joinWithAnd($parts);
    }

    /*
    |--------------------------------------------------------------------------
    | System status
    |--------------------------------------------------------------------------
    */

    private function systemStatus(Carbon $now): array
    {
        $lines = [
            $this->roundUpStatus($now),
            $this->suggestedOrdersStatus($now),
            $this->focusAlertStatus($now),
            $this->mobileAppStatus($now),
            $this->aiUsage($now),
        ];

        $needsCheck = collect($lines)->whereIn('level', ['critical', 'warning'])->count();

        return [
            'lines'   => $lines,
            'summary' => match (true) {
                $needsCheck === 0 => 'Everything ran on time.',
                $needsCheck === 1 => '1 item needs a check.',
                default           => $needsCheck . ' items need a check.',
            },
        ];
    }

    private function roundUpStatus(Carbon $now): array
    {
        $line   = ['key' => 'roundup', 'label' => 'Morning Round-Up', 'link' => null, 'alert' => null];
        $digest = DailyDigest::latestDigest();
        $dueBy  = $now->copy()->setTime(7, 30);

        if (!$digest) {
            return $line + [
                'level'  => $now->gte($dueBy) ? 'warning' : 'neutral',
                'chip'   => 'Not yet',
                'detail' => 'No Round-Up written yet. It runs daily at 7:00 AM.',
            ];
        }

        if ($digest->digest_date->isSameDay($now)) {
            $sentAt = 'Sent ' . $digest->updated_at->format('g:i A');

            if ($digest->source === 'bedrock') {
                return $line + ['level' => 'ok', 'chip' => 'On time', 'detail' => $sentAt . ' · written by AI'];
            }

            return $line + [
                'level'  => 'warning',
                'chip'   => 'Check',
                'detail' => $digest->error
                    ? $sentAt . ' using the standard template. The AI call failed.'
                    : $sentAt . ' using the standard template. AI is not set up.',
            ];
        }

        $last = $digest->digest_date->format('j M Y');

        if ($now->lt($dueBy)) {
            return $line + ['level' => 'ok', 'chip' => 'On time', 'detail' => 'Next one at 7:00 AM. Last one ' . $last . '.'];
        }

        // array_merge, not +: $line already holds 'alert' => null.
        return array_merge($line, [
            'level'  => 'critical',
            'chip'   => 'Failed',
            'detail' => 'Not written today. Last one ' . $last . '.',
            'alert'  => 'Morning Round-Up was not written today',
        ]);
    }

    private function suggestedOrdersStatus(Carbon $now): array
    {
        $line     = ['key' => 'suggested', 'label' => 'Suggested Orders', 'link' => null, 'alert' => null];
        $runs     = ScheduledRuns::last('recommendation:refresh');
        $computed = OutletRecommendation::max('computed_at');
        $computed = $computed ? Carbon::parse($computed) : null;
        $outlets  = OutletRecommendation::count();

        if ($runs['failed'] && (!$runs['ok'] || $runs['failed']->gt($runs['ok']))) {
            return array_merge($line, [
                'level'  => 'critical',
                'chip'   => 'Failed',
                'detail' => 'Last run failed · ' . $this->when($runs['failed'], $now) . '.'
                    . ($computed ? ' Last refresh ' . $this->when($computed, $now) . '. Suggestions may be out of date.' : ''),
                'alert'  => 'Suggested Orders did not refresh',
            ]);
        }

        if (!$computed) {
            return $line + [
                'level'  => 'neutral',
                'chip'   => 'Not run yet',
                'detail' => 'No suggestions yet. They refresh daily at 2:45 AM.',
            ];
        }

        if ($computed->gte($now->copy()->subHours(26))) {
            return $line + [
                'level'  => 'ok',
                'chip'   => 'On time',
                'detail' => 'Refreshed ' . $this->when($computed, $now) . ' · ' . $this->count($outlets, 'outlet', 'outlets'),
            ];
        }

        return $line + [
            'level'  => 'warning',
            'chip'   => 'Late',
            'detail' => 'Last refresh ' . $this->when($computed, $now) . '. It runs daily at 2:45 AM.',
        ];
    }

    private function focusAlertStatus(Carbon $now): array
    {
        $line    = ['key' => 'focus', 'label' => 'Focus Alert check', 'link' => null, 'alert' => null];
        $runs    = ScheduledRuns::last('tasks:check-risk');
        $flagged = Tasks::where('at_risk_at', '>=', $now->copy()->startOfDay())->count();
        $today   = $this->count($flagged, 'task', 'tasks') . ' flagged at risk today';

        if ($runs['failed'] && (!$runs['ok'] || $runs['failed']->gt($runs['ok']))) {
            return array_merge($line, [
                'level'  => 'critical',
                'chip'   => 'Failed',
                'detail' => 'Last run failed · ' . $this->when($runs['failed'], $now) . '. It runs every hour.',
                'alert'  => 'Focus Alert check failed',
            ]);
        }

        if (!$runs['ok']) {
            return $line + [
                'level'  => 'neutral',
                'chip'   => 'No record',
                'detail' => 'No run recorded yet. It runs every hour. ' . $today . '.',
            ];
        }

        if ($runs['ok']->gte($now->copy()->subHours(2))) {
            return $line + [
                'level'  => 'ok',
                'chip'   => 'On time',
                'detail' => 'Last run ' . $this->when($runs['ok'], $now) . ' · ' . $today,
            ];
        }

        return $line + [
            'level'  => 'warning',
            'chip'   => 'Late',
            'detail' => 'Last run ' . $this->when($runs['ok'], $now) . '. It runs every hour.',
        ];
    }

    private function mobileAppStatus(Carbon $now): array
    {
        $minVersion = GeneralSetting::where('key', 'min_version')->value('value');
        $errors     = AppError::where('created_at', '>=', $now->copy()->subDays(7))->count();
        $version    = $minVersion ? 'Min. version ' . $minVersion . ' · ' : '';

        return [
            'key'    => 'mobile',
            'label'  => 'Mobile app',
            'level'  => $errors > 0 ? 'warning' : 'ok',
            'chip'   => $errors > 0 ? 'Check' : 'No issues',
            'detail' => $version . ($errors > 0 ? $this->count($errors, 'error', 'errors') . ' in 7 days' : 'no errors in 7 days'),
            'link'   => $errors > 0 ? ['text' => 'View errors', 'url' => route('admin.app-errors', ['since' => '7d'])] : null,
            'alert'  => null,
        ];
    }

    /** Tokens the Morning Round-Up used this month. Not a status: no chip. */
    private function aiUsage(Carbon $now): array
    {
        $line = ['key' => 'ai', 'label' => 'AI usage', 'level' => 'info', 'chip' => null, 'link' => null, 'alert' => null];

        if (!$this->claude->isConfigured()) {
            return $line + ['detail' => 'AI is not set up. The Morning Round-Up uses the standard template.'];
        }

        $month   = DailyDigest::whereDate('digest_date', '>=', $now->copy()->startOfMonth()->toDateString());
        $tokens  = (int) (clone $month)->sum('input_tokens') + (int) (clone $month)->sum('output_tokens');
        $written = (clone $month)->where('source', 'bedrock')->count();

        return $line + [
            'detail' => number_format($tokens) . ' tokens this month · ' . $this->count($written, 'Morning Round-Up', 'Morning Round-Ups') . ' by AI',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Headline numbers
    |--------------------------------------------------------------------------
    */

    private function pulse(array $summary, array $people, Carbon $now): array
    {
        $weekStart  = $now->copy()->subDays(6)->startOfDay();
        $prevStart  = $weekStart->copy()->subDays(7);
        $prevVisits = IFEReport::where('created_at', '>=', $prevStart)->where('created_at', '<', $weekStart)->count();

        // Last month up to the same day of the month, so both periods are the same length.
        $lastMonth  = $now->copy()->subMonthNoOverflow();
        $prevOrders = (float) Order::confirmed()
            ->whereBetween('order_date', [$lastMonth->copy()->startOfMonth()->toDateString(), $lastMonth->toDateString()])
            ->sum('total_amount');

        $visits = (int) $summary['visits']['last_7d'];
        $orders = (float) $summary['orders']['month_value'];

        return [
            'people' => [
                'signed_in' => $people['signed_in'],
                'active'    => $people['active'],
                'share'     => $people['active'] ? (int) floor($people['signed_in'] / $people['active'] * 100) : 0,
            ],
            'visits' => [
                'value'  => $visits,
                'change' => $this->change($visits, $prevVisits, [
                    'more'   => 'more',
                    'less'   => 'fewer',
                    'period' => 'the previous 7 days',
                    'none'   => 'None in the previous 7 days',
                ]),
            ],
            'orders' => [
                'value'  => $orders,
                'count'  => (int) $summary['orders']['month_count'],
                'change' => $this->change($orders, $prevOrders, [
                    'more'   => 'more',
                    'less'   => 'less',
                    'period' => 'this time last month',
                    'none'   => 'None by this time last month',
                ]),
            ],
            'focus'  => [
                'at_risk' => (int) $summary['tasks']['at_risk'],
                'overdue' => (int) $summary['tasks']['overdue'],
            ],
        ];
    }

    /**
     * "12% more than the previous 7 days", "5% fewer than ...", "Same as ...",
     * or the "none" wording when the earlier period had nothing to compare.
     *
     * @param array{more: string, less: string, period: string, none: string} $words
     */
    private function change(float $current, float $previous, array $words): array
    {
        if ($previous <= 0) {
            return $current > 0
                ? ['direction' => 'up',   'text' => $words['none']]
                : ['direction' => 'flat', 'text' => 'Same as ' . $words['period']];
        }

        $percent = (int) round(($current - $previous) / $previous * 100);

        return match (true) {
            $percent > 0 => ['direction' => 'up',   'text' => $percent . '% ' . $words['more'] . ' than ' . $words['period']],
            $percent < 0 => ['direction' => 'down', 'text' => abs($percent) . '% ' . $words['less'] . ' than ' . $words['period']],
            default      => ['direction' => 'flat', 'text' => 'Same as ' . $words['period']],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | People and adoption
    |--------------------------------------------------------------------------
    */

    /**
     * Every account, needs-attention first: quiet reps, then people the app
     * cannot notify, then everyone else by name, inactive accounts last.
     * Sign-ins and app setup only; never locations.
     */
    private function people(Carbon $now): array
    {
        $weekStart = $now->copy()->subDays(6)->startOfDay();
        $quietFrom = $now->copy()->subDays(self::QUIET_DAYS);

        $visitsBy = IFEReport::where('created_at', '>=', $weekStart)
            ->selectRaw('created_by, count(*) as total')
            ->groupBy('created_by')
            ->pluck('total', 'created_by');

        $rows = User::query()
            ->get(['id', 'name', 'type', 'team', 'status', 'last_login_date', 'fcm_token'])
            ->map(function (User $user) use ($now, $quietFrom, $visitsBy) {
                $active    = (int) $user->status === 1;
                $isRep     = $user->type === User::TYPE_USER;
                $lastLogin = $user->last_login_date ? Carbon::parse($user->last_login_date) : null;
                $quiet     = $active && $isRep && (!$lastLogin || $lastLogin->lt($quietFrom));
                $appReady  = !empty($user->fcm_token);
                // Managers get Focus Alerts on the app too; admins work on the web.
                $noApp     = $active && $user->type !== User::TYPE_ADMIN && !$appReady;

                $rank = match (true) {
                    !$active         => 4,
                    $quiet && $noApp => 0,
                    $quiet           => 1,
                    $noApp           => 2,
                    default          => 3,
                };

                return [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'initials'   => $this->initials($user->name),
                    'role'       => $user->type === User::TYPE_USER ? 'Field rep' : User::getUserType($user->type),
                    'role_key'   => [User::TYPE_ADMIN => 'admin', User::TYPE_MANAGER => 'manager', User::TYPE_USER => 'rep'][$user->type] ?? 'rep',
                    'team'       => Helper::getTeam($user->team) ?: '—',
                    'active'     => $active,
                    'last_login' => $lastLogin,
                    'seen'       => $this->relative($lastLogin, $now),
                    'seen_exact' => $lastLogin ? 'Last sign-in ' . $lastLogin->format('j M Y, g:i A') : 'Has never signed in',
                    'minutes'    => $lastLogin ? $lastLogin->diffInMinutes($now) : PHP_INT_MAX,
                    'quiet'      => $quiet,
                    'app_ready'  => $appReady,
                    'no_app'     => $noApp,
                    'visits_7d'  => $isRep ? (int) ($visitsBy[$user->id] ?? 0) : null,
                    'rank'       => $rank,
                ];
            })
            ->sort(fn ($a, $b) => [$a['rank'], $a['rank'] <= 2 ? -$a['minutes'] : 0, $a['name']]
                              <=> [$b['rank'], $b['rank'] <= 2 ? -$b['minutes'] : 0, $b['name']])
            ->values();

        $active = $rows->where('active', true);

        return [
            'rows'      => $rows->all(),
            'total'     => $rows->count(),
            'counts'    => [
                'all'     => $rows->count(),
                'rep'     => $rows->where('role_key', 'rep')->count(),
                'manager' => $rows->where('role_key', 'manager')->count(),
                'admin'   => $rows->where('role_key', 'admin')->count(),
            ],
            'active'    => $active->count(),
            'signed_in' => $active->filter(fn ($row) => $row['last_login'] && $row['last_login']->gte($quietFrom))->count(),
            'shown'     => self::PEOPLE_ROWS,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Data health
    |--------------------------------------------------------------------------
    */

    private function dataHealth(): array
    {
        $outlets  = Leads::count();
        $located  = Leads::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $profiled = Leads::whereNotNull('business_category')
            ->whereNotNull('segment')
            ->whereNotNull('size_band')
            ->whereNotNull('seats')
            ->count();
        $assigned = Leads::whereNotNull('assign_to')->count();
        $products = Product::active()->count();
        $priced   = Product::active()->where('unit_price', '>', 0)->count();

        return [
            'unassigned' => $outlets - $assigned,
            'items'      => [
                $this->healthItem('Outlets with a location stamp', null, $located, $outlets, 'outlet', 'outlets',
                    fn ($missing) => ['text' => 'View ' . $missing . ' ' . ($missing === 1 ? 'outlet' : 'outlets'), 'url' => route('lead.index', ['gap' => 'no_location'])]),
                $this->healthItem('Outlets with a full profile', 'Suggested Orders work best with type, size, segment and seats.', $profiled, $outlets, 'outlet', 'outlets',
                    fn ($missing) => ['text' => 'View ' . $missing . ' ' . ($missing === 1 ? 'outlet' : 'outlets'), 'url' => route('lead.index', ['gap' => 'no_profile'])]),
                $this->healthItem('Outlets with a rep assigned', null, $assigned, $outlets, 'outlet', 'outlets',
                    fn ($missing) => ['text' => 'Assign ' . $missing . ' ' . ($missing === 1 ? 'outlet' : 'outlets'), 'url' => route('lead.index', ['gap' => 'unassigned'])]),
                $this->healthItem('Active products with a price', null, $priced, $products, 'product', 'products',
                    fn ($missing) => ['text' => 'View products', 'url' => route('product.index')]),
            ],
        ];
    }

    private function healthItem(string $label, ?string $note, int $done, int $total, string $one, string $many, callable $fix): array
    {
        $missing = $total - $done;
        // Floor, not round: 99.6% must not read as 100% while a gap remains.
        $share   = $total > 0 ? ($missing === 0 ? 100 : (int) floor($done / $total * 100)) : 0;

        $status = match (true) {
            $total === 0               => ['key' => 'none', 'text' => 'Nothing yet'],
            $missing === 0             => ['key' => 'done', 'text' => 'All set'],
            $share >= self::GOOD_SHARE => ['key' => 'good', 'text' => 'Good'],
            $share >= self::FAIR_SHARE => ['key' => 'fair', 'text' => 'Fair'],
            default                    => ['key' => 'low',  'text' => 'Low'],
        };

        return [
            'label'  => $label,
            'note'   => $note,
            'share'  => $share,
            'status' => $status,
            'count'  => $total === 0 ? 'No ' . $many . ' yet' : $done . ' of ' . $this->count($total, $one, $many),
            'fix'    => $missing > 0 ? $fix($missing) : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Outlet coverage by area
    |--------------------------------------------------------------------------
    */

    /** Per area: outlets, and how many had a visit in the last 30 days. Best covered first. */
    private function coverage(Carbon $now): array
    {
        $since = $now->copy()->subDays(30);

        $counts = Leads::query()
            ->select('ife_area_id')
            ->selectRaw('count(*) as outlets')
            ->selectRaw(
                'sum(case when exists (select 1 from ife_report where ife_report.lead_id = leads.id and ife_report.created_at >= ?) then 1 else 0 end) as visited',
                [$since]
            )
            ->groupBy('ife_area_id')
            ->get()
            ->keyBy(fn ($row) => (int) $row->ife_area_id);

        $row = fn (string $name, $stats) => [
            'name'    => $name,
            'outlets' => (int) optional($stats)->outlets,
            'visited' => (int) optional($stats)->visited,
            'share'   => optional($stats)->outlets ? (int) round($stats->visited / $stats->outlets * 100) : 0,
        ];

        $areas = IfeArea::orderBy('area')->get(['id', 'area'])
            ->map(fn (IfeArea $area) => $row($area->area, $counts->get($area->id)));

        if ($counts->has(0)) {
            $areas->push($row('No area', $counts->get(0)));
        }

        return $areas
            ->sort(fn ($a, $b) => [$b['outlets'] > 0, $b['share'], $a['name']] <=> [$a['outlets'] > 0, $a['share'], $b['name']])
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Setup at a glance
    |--------------------------------------------------------------------------
    */

    private function setup(array $people): array
    {
        $counts = $people['counts'];

        return [
            'people'   => [
                'total'  => $counts['all'],
                'detail' => implode(' · ', [
                    $this->count($counts['admin'], 'admin', 'admins'),
                    $this->count($counts['manager'], 'manager', 'managers'),
                    $this->count($counts['rep'], 'rep', 'reps'),
                ]),
            ],
            'forms'    => [
                'total'   => Form::count(),
                'enabled' => Form::where('is_enabled', true)->count(),
            ],
            'products' => [
                'active'     => Product::active()->count(),
                'inactive'   => Product::where('is_active', false)->count(),
                'categories' => Product::active()->whereNotNull('category')->distinct()->count('category'),
            ],
            'areas'    => [
                'total'   => IfeArea::count(),
                'outlets' => Leads::count(),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Wording helpers (brand voice: numerals, "28 Sep 2026", "2:30 PM")
    |--------------------------------------------------------------------------
    */

    private function count(int $n, string $one, string $many): string
    {
        return number_format($n) . ' ' . ($n === 1 ? $one : $many);
    }

    /** "Today, 8:12 AM", "Yesterday, 6:05 PM", "9 days ago", "3 months ago", "Never". */
    private function relative(?Carbon $at, Carbon $now): string
    {
        if (!$at) {
            return 'Never';
        }

        $days = $at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay());

        return match (true) {
            $days === 0 => 'Today, ' . $at->format('g:i A'),
            $days === 1 => 'Yesterday, ' . $at->format('g:i A'),
            $days < 30  => $days . ' days ago',
            $days < 365 => max(1, $at->diffInMonths($now)) . ' ' . (max(1, $at->diffInMonths($now)) === 1 ? 'month' : 'months') . ' ago',
            default     => 'Over a year ago',
        };
    }

    /** "2:45 AM" today, "yesterday, 2:45 AM", else "27 Sep 2026, 2:45 AM". */
    private function when(Carbon $at, Carbon $now): string
    {
        $days = $at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay());

        return match ($days) {
            0       => $at->format('g:i A'),
            1       => 'yesterday, ' . $at->format('g:i A'),
            default => $at->format('j M Y, g:i A'),
        };
    }

    /** "today", "yesterday", "3 days ago". */
    private function daysAgo(Carbon $at, Carbon $now): string
    {
        $days = $at->copy()->startOfDay()->diffInDays($now->copy()->startOfDay());

        return match ($days) {
            0       => 'today',
            1       => 'yesterday',
            default => $days . ' days ago',
        };
    }

    /** "A", "A and B", "A, B and C", "A, B and 2 more". */
    private function nameList(Collection $names): string
    {
        if ($names->count() > 3) {
            return $names->take(2)->implode(', ') . ' and ' . ($names->count() - 2) . ' more';
        }

        return $this->joinWithAnd($names->all());
    }

    private function joinWithAnd(array $parts): string
    {
        $parts = array_values($parts);

        if (count($parts) <= 1) {
            return $parts[0] ?? '';
        }

        return implode(', ', array_slice($parts, 0, -1)) . ' and ' . end($parts);
    }

    private function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];

        return mb_strtoupper(mb_substr($words[0] ?? '', 0, 1) . mb_substr($words[1] ?? '', 0, 1));
    }
}
