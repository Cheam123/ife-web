<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FlushFcmTokens extends Command
{
    protected $signature = 'fcm:flush-tokens {--force : Skip the confirmation prompt}';
    protected $description = 'Clear every stored FCM token (run after switching Firebase project)';

    public function handle()
    {
        $count = User::whereNotNull('fcm_token')->count();

        if ($count === 0) {
            $this->info('No FCM tokens stored, nothing to clear.');
            return self::SUCCESS;
        }

        if (! $this->option('force')
            && ! $this->confirm("Clear the FCM token for {$count} user(s)?")) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        // Query-builder update, so it bypasses $fillable (fcm_token is not fillable
        // on User) and touches every row in one statement.
        $cleared = User::whereNotNull('fcm_token')->update(['fcm_token' => null]);

        $this->info("Cleared {$cleared} FCM token(s). Devices re-register on next app launch.");

        return self::SUCCESS;
    }
}
