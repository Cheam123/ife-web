<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads');
            $table->string('task_reference', 21)->nullable()->unique()->comment('T-Ymd-0001, see Tasks::nextReference()');
            $table->string('title', 500)->default('N/A')->index();
            $table->integer('status')->nullable()->index()->comment('1:New | 2:In Progress | 3:Done | 4:Verified | 5:Completed | 6:KIV | 7:Rejected | 8:On Hold');
            $table->tinyInteger('alert')->default(0)->index();
            $table->tinyInteger('due_notify')->default(0)->index()->comment('1 once the overdue alert has been sent');
            $table->string('invoice_no', 100)->nullable();
            $table->decimal('sales', 12, 2)->nullable()->index();
            $table->date('start_date')->nullable()->index();
            $table->time('start_time')->nullable()->index();
            $table->date('due_date')->nullable()->index();
            $table->time('due_time')->nullable()->index();
            $table->dateTime('appointment_date')->nullable()->index();
            $table->text('remark')->nullable();
            $table->dateTime('creation_date')->nullable()->index();
            $table->dateTime('inprogress_date')->nullable()->index();
            $table->dateTime('done_date')->nullable()->index();
            $table->dateTime('verify_date')->nullable()->index();
            $table->dateTime('complete_date')->nullable()->index();
            $table->dateTime('reject_date')->nullable()->index();
            $table->dateTime('kiv_date')->nullable()->index();
            $table->dateTime('onhold_date')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
