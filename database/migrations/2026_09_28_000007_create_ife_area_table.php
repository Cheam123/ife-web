<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ife_area', function (Blueprint $table) {
            $table->id();
            $table->string('area', 30);
            $table->string('description', 500);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ife_area');
    }
};
