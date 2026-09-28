<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 10)->nullable()->unique()->comment('Staff ID, e.g. U00012 (login is by email)');
            $table->integer('type')->default(2)->index()->comment('0:Admin | 1:Manager | 2:User');
            $table->string('telegram_chat_id', 50)->nullable()->index();
            $table->integer('team')->nullable()->index()->comment('See Helper::getTeamListing()');
            $table->string('email')->unique();
            $table->string('mobile', 50)->nullable()->unique()->comment('user mobile number');
            $table->timestamp('email_verified_at')->nullable();
            $table->dateTime('last_login_date')->nullable();
            $table->string('password')->nullable();
            $table->integer('status')->default(1)->index()->comment('1:Active | 2:Inactive');
            $table->boolean('enable_notification')->nullable()->default(true);
            $table->char('gender', 1)->default('F')->comment('F:Female | M:Male');
            $table->string('fcm_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
