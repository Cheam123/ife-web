<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every submission is a case (a "record", shown as REC-####). A case may
     * follow up on ONE earlier case of the same form via parent_submission_id;
     * a closed parent can still gain children.
     */
    public function up()
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('forms')->cascadeOnDelete();
            // nullOnDelete: deleting a parent must orphan its children, never
            // delete them — they are real work records.
            $table->foreignId('parent_submission_id')->nullable()->constrained('form_submissions')->nullOnDelete();
            $table->string('record_title')->nullable();
            $table->string('record_status')->default('open')->comment('open | closed');
            // Closing a record cancels whatever is still in flight, so it has to
            // be accountable: who closed it, when, and why.
            $table->text('record_closed_remark')->nullable();
            $table->unsignedBigInteger('record_closed_by')->nullable();
            $table->timestamp('record_closed_at')->nullable();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->json('form_elements');
            // Copy of the form's process at submit time; the form may change afterwards.
            $table->json('process_snapshot')->nullable();
            // Answers archived each time a repeat loop starts a new round.
            $table->json('round_snapshots')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejected_remark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_submissions');
    }
};
