<?php

namespace App\Http\Controllers\MobileApp;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Tasks;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Provide a dashboard summary for the authenticated mobile user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Active (new and in-progress) tasks the user can see
        $taskQuery = Tasks::visibleTo($user)->whereIn('status', [1, 2]);

        // 2. Get due ranges
        $today       = now()->toDateString();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek   = now()->endOfWeek();

        $overdueCount  = (clone $taskQuery)->where('due_date', '<', $today)->count();
        $dueTodayCount = (clone $taskQuery)->where('due_date', $today)->count();
        $dueWeekCount  = (clone $taskQuery)->whereBetween('due_date', [$startOfWeek, $endOfWeek])->count();

        // 3. Determine Task Subtitle
        $subtitle = "You have {$overdueCount} tasks overdue !";
        if ($dueTodayCount > 0) {
            $subtitle = $dueTodayCount === 1 ? "You have {$dueTodayCount} task due today" : "You have {$dueTodayCount} tasks due today";
        } elseif ($dueWeekCount > 0) {
            $subtitle = $dueWeekCount === 1 ? "You have {$dueWeekCount} task due this week" : "You have {$dueWeekCount} tasks due this week";
        }

        // 4. User permissions — decided by user type (Admin / Manager / User)
        $listOfPermissions = $user->abilities();
        $listOfPermissions['can_assign_subscribers'] = $user->can('create_task');

        $data = [
            'salesTasks'        => [
                'count'    => $dueTodayCount === 0 ? $dueWeekCount : $dueTodayCount,
                'subtitle' => $subtitle
            ],
            'listOfPermissions' => $listOfPermissions,
        ];

        return $this->response_ok($data, 'Dashboard summary retrieved successfully.');
    }

    public function getLifeVersion(Request $request)
    {
        $lifeVersion = GeneralSetting::where('key', 'life_version')->value('value');
        // The oldest build still allowed to run. Sent alongside the current
        // version so one call answers both "is there an update?" and "must I
        // take it before going any further?".
        $minVersion  = GeneralSetting::where('key', 'min_version')->value('value');

        return $this->response_ok([
            'life_version' => $lifeVersion,
            'min_version'  => $minVersion,
        ], 'Life app version retrieved successfully.');
    }

    public function getAppDownloadLink(Request $request)
    {
        $downloadLink = GeneralSetting::where('key', 'life_app_download_link')->value('value');

        return $this->response_ok(['download_link' => $downloadLink], 'Life app download link retrieved successfully.');
    }

}
