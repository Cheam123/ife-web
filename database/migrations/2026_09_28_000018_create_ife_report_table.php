<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Field visit (IFE) reports, filed from the mobile app. A report may be
     * attached to a task, or converted into a new lead + task later.
     */
    public function up()
    {
        Schema::create('ife_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('created_by')->constrained('users');
            $table->char('freeze', 1)->default('N')->comment('Y:Frozen | N:Open');
            $table->string('company_name')->nullable();
            $table->string('nature_of_business')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('ife_area')->nullable()->constrained('ife_area')->cascadeOnDelete();
            $table->string('shop_name')->nullable();
            $table->text('problem_description')->nullable();
            $table->string('support_required')->nullable();
            $table->text('support_description')->nullable();
            $table->text('personal_remarks')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('other_mobile_numbers')->nullable();
            $table->string('email')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->text('next_followup_plan')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ife_report');
    }
};
