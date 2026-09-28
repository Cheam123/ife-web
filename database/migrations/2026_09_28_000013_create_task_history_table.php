<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail of task changes; content_before/after hold the serialized
     * field diff built by Helper::prepareDataForSerialize().
     */
    public function up()
    {
        Schema::create('task_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->tinyInteger('before_status')->comment('Status before user submit the action');
            $table->tinyInteger('after_status')->comment('Status after user submit the action');
            $table->text('content_before')->nullable();
            $table->text('content_after')->nullable();
            $table->text('remark')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_history');
    }
};
