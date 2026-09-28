<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\fireEmailNotification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class GenerateRandomPassword
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        //
        $user = $event->user;

        $password = Str::random(8);

        $user->password = Hash::make($password);

        if (!$user->save()) {
            abort(500, 'Error saving password for user.');
        }

        $runJob = (new fireEmailNotification('1', $user, $password));
        dispatch($runJob);
    }
}
