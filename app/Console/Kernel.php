<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\ClearZipStorage;
use App\Console\Commands\ResetAppSubmitCount;
use App\Services\ScheduledRuns;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('telegram:reset-telegram-pending-update-count')->dailyAt('00:35');
        // $schedule->command('user:reset-telegram-chat-id')->dailyAt('01:00');
        $schedule->command('zip:clear')->dailyAt('01:30');
        // Attachments upload before a form is submitted, so abandoning a
        // half-filled form leaves the bytes behind with nothing pointing at them.
        $schedule->command('form:clear-orphan-uploads')->dailyAt('01:45');
        $schedule->command('task:reset-submit-count')->dailyAt('02:15');
        // Nightly: every outlet's product recommendations (a single outlet is
        // also refreshed whenever its profile or orders change).
        $this->tracked($schedule->command('recommendation:refresh')->dailyAt('02:45'), 'recommendation:refresh');
        // Morning briefing for managers on the dashboard.
        $this->tracked($schedule->command('digest:daily')->dailyAt('07:00'), 'digest:daily');
        // Flags tasks at risk / overdue and pushes the new ones to managers.
        $this->tracked($schedule->command('tasks:check-risk')->hourly(), 'tasks:check-risk');
    }

    /** Records each run's outcome for the admin Overview's System status. */
    private function tracked(Event $event, string $command): Event
    {
        return $event
            ->onSuccess(fn () => ScheduledRuns::record($command, true))
            ->onFailure(fn () => ScheduledRuns::record($command, false));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
