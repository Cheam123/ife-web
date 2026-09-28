<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Attachments for form answers, following the same shape as
 * ife_report_document_uploads and document_uploads: one row per file, keyed to
 * its parent, with the metadata a listing needs.
 *
 * The one addition those tables do not need is `element_id`. IFE and Task each
 * have a single implicit bucket of files; a dynamic form can carry several
 * `file` fields, so every attachment has to say which field it answers.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('form_submission_document_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_submission_id')->constrained('form_submissions')->cascadeOnDelete();
            $table->string('element_id', 64)->comment('The `file` element this answers');
            // Stored, not derived: a row can be written in a different month
            // than the file it points at, and a guessed path is a dead link.
            $table->string('path', 190)->comment('Folder on the public disk');
            $table->string('filename', 190)->comment('Name on disk');
            $table->string('original_name', 190)->nullable()->comment('Name the user saw');
            $table->string('mime_type', 120)->nullable();
            $table->bigInteger('size')->nullable()->comment('File size in bytes');
            $table->foreignId('upload_by')->nullable()->constrained('users');
            $table->timestamps();

            // Named explicitly: the generated name would exceed MySQL's
            // 64-character identifier limit.
            $table->index(['form_submission_id', 'element_id'], 'fsdu_submission_element_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_submission_document_uploads');
    }
};
