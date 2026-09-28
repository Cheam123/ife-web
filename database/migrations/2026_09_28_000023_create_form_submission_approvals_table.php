<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per step of a submission's process (handler, approval or CC).
     * A loop re-materialises its steps each round, so the same node can have
     * several rows; `iteration` says which round a row belongs to.
     */
    public function up()
    {
        Schema::create('form_submission_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->constrained('form_submissions')->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->unsignedInteger('iteration')->default(1);
            // Snapshot of the process node that produced this row; the form's
            // process_definition may change after submission, so these copies
            // are authoritative for in-flight approvals.
            $table->string('node_id')->nullable();
            $table->string('node_type')->default('approval'); // fill | approval | cc
            $table->string('source_branch_id')->nullable();   // process branch node, not an office branch
            $table->string('name');
            $table->string('approval_mode')->default('any');  // any | all
            $table->json('approver_ids')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable(); // who picked the handler on a runtime-assigned step
            $table->json('field_permissions')->nullable();
            $table->string('status')->default('pending');     // pending | approved | rejected | skipped | notified
            $table->json('actions')->nullable();
            $table->unsignedBigInteger('acted_by')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index(['form_submission_id', 'sequence']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_submission_approvals');
    }
};
