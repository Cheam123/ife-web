<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Errors reported by the mobile app (POST /api/mobile/error/log).
     */
    public function up()
    {
        Schema::create('error_table', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->nullable();
            $table->text('error_message')->nullable();
            $table->text('input')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('error_table');
    }
};
