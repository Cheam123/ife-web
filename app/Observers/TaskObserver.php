<?php

namespace App\Observers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskObserver
{
    /*
    |--------------------------------------------------------------------------
    | Task Status
    |--------------------------------------------------------------------------
    |  1=Incomplete
    |  2=Submitted
    |  3=In Progress
    |  4=Rejected
    |  5=Ready for Approval
    |  6=Approved
    |  9=Completed
    |--------------------------------------------------------------------------
    */

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the Task "created" event.
     *
     * @param  \App\Models\Tasks  $task
     * @return void
     */
    public function created(Tasks $task)
    {
        //
    }

    /**
     * Handle the Tasks "updated" event.
     *
     * @param  \App\Models\ApplTasksication  $task
     * @return void
     */
    public function updated(Tasks $task)
    {
        //
    }

    /**
     * Handle the Tasks "deleted" event.
     *
     * @param  \App\Models\Tasks  $task
     * @return void
     */
    public function deleted(Tasks $task)
    {
        //
    }

    /**
     * Handle the Tasks "restored" event.
     *
     * @param  \App\Models\Tasks  $task
     * @return void
     */
    public function restored(Tasks $task)
    {
        //
    }

    /**
     * Handle the Tasks "force deleted" event.
     *
     * @param  \App\Models\Tasks  $task
     * @return void
     */
    public function forceDeleted(Tasks $task)
    {
        //
    }
}
