<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetTelegramChatId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-telegram-chat-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset telegram chat id.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereNotIn('status',['1'])->whereNotNull('telegram_chat_id')->get();

        $users->each(function ($usr) {
            $usr->telegram_chat_id = NULL;
            $usr->save();
        });

        $users = User::onlyTrashed()->whereNotNull('telegram_chat_id')->get();

        $users->each(function ($usr) {
            $usr->telegram_chat_id = NULL;
            $usr->save();
        });

        $this->info('Cleared telegram chat id for all blocked users!');
    }
}
