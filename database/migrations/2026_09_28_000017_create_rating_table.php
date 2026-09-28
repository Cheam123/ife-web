<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rating given to a task's subscriber / sub-subscribers.
     */
    public function up()
    {
        Schema::create('rating', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->dateTime('task_creation_date')->nullable()->index();
            $table->decimal('rate', 5, 2)->nullable()->index();
            $table->tinyInteger('editable')->default(1)->index()->comment('0:close for edit 1:open for edit');
            $table->string('comment', 1000)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rating');
    }
};
