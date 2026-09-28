<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Files attached to a lead, a task, or a task chat message. Stored under
     * lead/{lead_id} or task/{task_id} (see DocumentUpload::getFilePathAttribute()).
     */
    public function up()
    {
        Schema::create('document_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads');
            $table->foreignId('task_id')->nullable()->constrained('tasks');
            $table->foreignId('task_comment_id')->nullable()->constrained('task_comment');
            $table->string('filename', 100)->nullable();
            $table->string('original_name')->nullable()->comment('Original filename as uploaded');
            $table->string('mime_type')->nullable()->comment('MIME Type of the file');
            $table->bigInteger('size')->comment('File Size in Bytes');
            $table->foreignId('upload_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_uploads');
    }
};
