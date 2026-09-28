<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ife_report_document_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ife_report_id')->constrained('ife_report');
            $table->string('filename', 100)->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->comment('File Size in Bytes');
            $table->foreignId('upload_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ife_report_document_uploads');
    }
};
