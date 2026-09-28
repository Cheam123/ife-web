<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Notifications\UserCreated as NotifyUserCreated;
use App\Events\UserCreated;

class fireEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public      $timeout = 120;
    protected   $eventType = 0;
    protected   $password;
    protected   $user;


    public function __construct($eventType, $object, $object2 = null, $object3 = null)
    {
        $this->eventType = $eventType;

        switch ($eventType) {
            case '1':
                $this->user = $object;
                $this->password = $object2;
            
                break;
        }
    }

    public function handle()
    {
        switch ($this->eventType) {
            case '1': // User Creation

                $this->user->notify(new NotifyUserCreated($this->password));
                break;
        }
    }
}
