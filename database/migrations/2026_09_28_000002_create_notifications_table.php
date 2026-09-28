<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            // The record the notification is about (e.g. a TaskComment), so unread badges can be looked up.
            $table->unsignedBigInteger('content_id')->nullable()->index()->comment('Notification content Id');
            $table->string('content_type')->nullable()->index()->comment('Content model');
            $table->integer('telegram_send_message_id')->nullable()->index();
            $table->string('telegram_send_sticker_id', 150)->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
