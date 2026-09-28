<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A user's personal reminder on a task, sent by the notification:task-reminder-alert command.
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->date('reminder_date')->index();
            $table->time('reminder_time')->index();
            $table->tinyInteger('notify')->default(0)->index()->comment('1 once the reminder has been sent');
            $table->string('message', 512)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reminders');
    }
};
