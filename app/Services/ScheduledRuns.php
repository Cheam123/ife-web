<?php

namespace App\Services;

use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;

/**
 * Remembers when each scheduled command last finished, so the admin
 * Overview can say whether the nightly and hourly jobs ran. The scheduler
 * calls record() from onSuccess / onFailure (see Console\Kernel). The times
 * live in general_settings under "last_run_ok:<command>" and
 * "last_run_failed:<command>".
 */
class ScheduledRuns
{
    public static function record(string $command, bool $succeeded, ?Carbon $at = null): void
    {
        $outcome = $succeeded ? 'ok' : 'failed';

        GeneralSetting::updateOrCreate(
            ['key' => "last_run_{$outcome}:{$command}"],
            [
                'value'       => ($at ?? Carbon::now())->toIso8601String(),
                'description' => "When the scheduled {$command} command last " . ($succeeded ? 'succeeded' : 'failed') . ' (admin Overview, System status)',
            ]
        );
    }

    /**
     * @return array{ok: ?Carbon, failed: ?Carbon}
     */
    public static function last(string $command): array
    {
        $values = GeneralSetting::whereIn('key', ["last_run_ok:{$command}", "last_run_failed:{$command}"])
            ->pluck('value', 'key');

        $parse = fn ($value) => $value ? Carbon::parse($value) : null;

        return [
            'ok'     => $parse($values["last_run_ok:{$command}"] ?? null),
            'failed' => $parse($values["last_run_failed:{$command}"] ?? null),
        ];
    }
}
