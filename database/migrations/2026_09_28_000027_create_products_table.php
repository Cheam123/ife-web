<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product catalogue, maintained by Admins. Order lines and product
     * recommendations point at these rows.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 150)->index();
            $table->string('category', 50)->nullable()->index();
            $table->string('unit', 20)->default('unit')->comment('What one quantity means: kg, bottle, box, unit ...');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
