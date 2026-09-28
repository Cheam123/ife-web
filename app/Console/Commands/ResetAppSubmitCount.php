<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use Illuminate\Console\Command;

class ResetAppSubmitCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:reset-submit-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the daily task reference running number.';

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
        GeneralSetting::where('key', 'task_submit_cnt')->update(['value' => 0]);

        $this->info('Cleared the task reference running number!');
    }
}
