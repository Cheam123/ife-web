<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who takes part in a task, and as what. A user may hold several roles on
     * the same task, one row each.
     */
    public function up()
    {
        Schema::create('task_users', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('task_id')->constrained('tasks');
            $table->smallInteger('role')->index()->comment('1:Creator | 2:Subscriber | 3:Checker | 4:Owner | 5:Viewer | 6:Sub-subscriber');
            $table->unique(['user_id', 'task_id', 'role']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_users');
    }
};
