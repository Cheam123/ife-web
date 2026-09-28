<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tasks;
use App\Models\GeneralSetting;
use App\Jobs\TelegramNotification;
use App\Helpers\Helper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;
use Config;
use Auth;

class DailySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Summary';

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
        $this->info("Daily Summary: " . Carbon::now());
        
        $token = GeneralSetting::where('key', 'telegram_api')->first()->value;
        $tasks = Tasks::with('users')
                      ->where('status', 2)
                      ->where('due_notify',0)
                      ->whereDate('due_date', '<=', Carbon::now()->format('Y-m-d'))
                      ->whereTime('due_time', '<=', Carbon::now()->format('H:i:s'))
                      ->get();

        foreach($tasks as $t) {
            $message =  
            "Task (" . $t->task_reference . ") has been overdue. \n\n" .
            "Subscriber: " . $t->users->where('role',2)->first()->user->name . "\n";

            $ids   = array_unique($t->users->pluck('user_id')->toArray());
            $users = User::whereIn('id',$ids)->where('status',1)->get();

            foreach($users as $u) {
                $r = TaskUsers::where('task_id',$this->task->id)
                                ->where('user_id',$u->id)
                                ->first();
                if ($r->role != 2) {
                    if (isset($u->telegram_chat_id)) {
                        sleep(1);
                        $url  = 'https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$u->telegram_chat_id.'&text='.urlencode($message).'&parse_mode=html';
                        Curl::to($url)->returnResponseObject()->get();
                    }
                }
            }

            $upd = Tasks::where('id',$t->id)->first();
            $upd->due_notify = 1;
            $upd->update();
        }
    }
}
