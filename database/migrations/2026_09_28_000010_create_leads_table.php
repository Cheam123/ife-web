<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id', 20)->nullable()->index();
            $table->string('name', 256)->nullable()->index();
            $table->string('business_name', 300)->nullable()->index();
            $table->date('receiving_date')->nullable();
            $table->integer('source')->nullable()->index()->comment('See Helper::getLeadSourceListing()');
            $table->integer('business_category')->nullable()->index()->comment('See Helper::getBusinessCategoryListing()');
            $table->foreignId('belong_to')->comment('Creator / owner')->constrained('users');
            $table->foreignId('assign_to')->nullable()->comment('Subscriber of the latest task')->constrained('users');
            $table->foreignId('hq_checker')->nullable()->comment('Admin/Manager who last created a task for it')->constrained('users');
            $table->string('email')->nullable()->index();
            $table->string('mobile', 15)->nullable()->index();
            $table->string('address', 256)->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->string('postcode', 5)->nullable();
            $table->foreignId('ife_area_id')->nullable()->constrained('ife_area');
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
