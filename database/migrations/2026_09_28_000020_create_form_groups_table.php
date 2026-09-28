<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Groups organise the form list once there are more forms than fit on a
 * screen. A group is a container only — it carries no permissions of its own,
 * because "who may submit this form" already lives on each form and a second
 * gate would just shadow it.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('form_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_groups');
    }
};
