<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Task chat messages. An IFE report can be posted into the chat, so
     * ife_report_id links back to it (indexed, but no FK: the report is
     * removed together with its comment in IFEReportController@delete).
     */
    public function up()
    {
        Schema::create('task_comment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks');
            $table->foreignId('submit_by')->constrained('users');
            $table->dateTime('submit_date')->nullable()->index();
            $table->longText('message')->nullable();
            $table->unsignedBigInteger('ife_report_id')->nullable()->index();
            $table->string('reply_message_id')->nullable()->comment('Message ID of the reply comment');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_comment');
    }
};
