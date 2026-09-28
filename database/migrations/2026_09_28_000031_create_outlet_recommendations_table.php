<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Precomputed product recommendations per outlet (recommendation:refresh).
     * The explanation is written lazily the first time someone opens it, then
     * cached here until the items change.
     */
    public function up()
    {
        Schema::create('outlet_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->unique()->constrained('leads')->cascadeOnDelete();
            $table->json('items')->comment('Ranked products with quantity, support and value');
            $table->json('neighbors')->comment('Similar outlets used: lead id => Gower distance');
            $table->string('items_hash', 64)->nullable()->comment('Changes when the recommended products change');
            $table->unsignedSmallInteger('gap_count')->default(0);
            $table->decimal('estimated_monthly_value', 12, 2)->default(0);
            $table->text('explanation')->nullable();
            $table->string('opening_line', 500)->nullable();
            $table->string('explanation_source', 20)->nullable()->comment('bedrock | template');
            $table->string('explained_hash', 64)->nullable()->comment('items_hash the explanation was written for');
            $table->dateTime('computed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outlet_recommendations');
    }
};
