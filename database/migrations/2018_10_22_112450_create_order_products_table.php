<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->default(0);
            $table->unsignedInteger('my_product_id')->nullable();
            $table->unsignedInteger('my_product_variant_id')->nullable();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price');
            $table->unsignedInteger('quantity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
}
