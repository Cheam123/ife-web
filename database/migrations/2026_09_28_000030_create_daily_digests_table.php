<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One AI-written manager digest per day (digest:daily), shown on the
     * dashboard. `source` says whether Claude wrote it or the template did
     * (Bedrock not configured, or the call failed).
     */
    public function up()
    {
        Schema::create('daily_digests', function (Blueprint $table) {
            $table->id();
            $table->date('digest_date')->unique();
            $table->text('content');
            $table->json('stats')->nullable()->comment('The figures the digest was written from');
            $table->string('source', 20)->default('template')->comment('bedrock | template');
            $table->string('model', 100)->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_digests');
    }
};
