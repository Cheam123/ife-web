<?php

namespace App\Services;

use App\Models\Tasks;
use Illuminate\Support\Carbon;

/**
 * Classifies an open task as on track, at risk or overdue.
 *
 *  - overdue:  the due date/time has passed
 *  - at risk:  not overdue, and either due within `hours`, or past
 *              `elapsed` (e.g. 75%) of its start -> due window
 *  - on track: everything else
 *
 * The single definition used by the dashboard, the mobile day view and the
 * scheduled tasks:check-risk alert, so they never disagree.
 */
final class TaskRisk
{
    public const ON_TRACK = 'on_track';
    public const AT_RISK  = 'at_risk';
    public const OVERDUE  = 'overdue';

    /** Statuses the check applies to: New and In Progress. */
    public const OPEN_STATUSES = [1, 2];

    public function __construct(
        private float $hours = 24.0,
        private float $elapsed = 0.75,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self((float) config('ife.risk.hours', 24), (float) config('ife.risk.elapsed', 0.75));
    }

    /**
     * @return array{level: string, reason: ?string, due_at: ?Carbon, hours_left: ?float, elapsed: ?float}
     */
    public function assess(?Carbon $start, ?Carbon $due, Carbon $now): array
    {
        $result = ['level' => self::ON_TRACK, 'reason' => null, 'due_at' => $due, 'hours_left' => null, 'elapsed' => null];

        if ($due === null) {
            return $result; // no deadline, nothing to be late for
        }

        $hoursLeft            = ($due->getTimestamp() - $now->getTimestamp()) / 3600;
        $result['hours_left'] = round($hoursLeft, 1);

        if ($hoursLeft < 0) {
            return ['level' => self::OVERDUE, 'reason' => 'Overdue'] + $result;
        }

        if ($start !== null && $due->greaterThan($start)) {
            $window            = $due->getTimestamp() - $start->getTimestamp();
            $used              = ($now->getTimestamp() - $start->getTimestamp()) / $window;
            $result['elapsed'] = round(max(0.0, $used), 2);
        }

        if ($hoursLeft <= $this->hours) {
            return ['level' => self::AT_RISK, 'reason' => 'Due within ' . $this->formatHours($hoursLeft)] + $result;
        }

        if ($result['elapsed'] !== null && $result['elapsed'] >= $this->elapsed) {
            return ['level' => self::AT_RISK, 'reason' => (int) round($result['elapsed'] * 100) . '% of the time used'] + $result;
        }

        return $result;
    }

    /** Assess a task row (due_date + due_time, start_date + start_time). */
    public function assessTask(Tasks $task, Carbon $now): array
    {
        return $this->assess(
            self::combine($task->start_date, $task->start_time, '00:00:00') ?? ($task->creation_date ? Carbon::parse($task->creation_date) : null),
            self::combine($task->due_date, $task->due_time, '23:59:59'),
            $now
        );
    }

    /** A date column plus an optional time column; the default time fills a missing one. */
    public static function combine($date, $time, string $defaultTime): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            $day = Carbon::parse($date)->toDateString();

            return Carbon::parse($day . ' ' . ($time ?: $defaultTime));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatHours(float $hours): string
    {
        if ($hours < 1) {
            return max(1, (int) round($hours * 60)) . ' min';
        }

        return $hours < 48 ? round($hours) . ' h' : round($hours / 24) . ' days';
    }
}
