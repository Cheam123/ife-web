<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders an outlet placed, one line per product. This is the outlet's
     * order history and the purchase data the recommendation engine learns from.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 20)->nullable()->unique()->comment('ORD-000001, set from the id after insert');
            $table->foreignId('lead_id')->constrained('leads');
            $table->date('order_date')->index();
            $table->foreignId('created_by')->constrained('users');
            $table->string('status', 20)->default('confirmed')->index()->comment('confirmed | cancelled');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2)->comment('Price at the time of the order');
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_lines');
        Schema::dropIfExists('orders');
    }
};
