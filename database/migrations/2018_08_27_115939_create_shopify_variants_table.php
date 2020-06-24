<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyVariantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('product_combinations', 'product_variants');

        Schema::create('shopify_product_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('shopify_variant_id');
            $table->unsignedInteger('product_variant_id')->nullable()->reference('id', '')->on('product_variants');
            $table->unsignedInteger('product_id')->reference('id')->on('products');
            $table->timestamps();
            $table->unique(['shopify_variant_id', 'product_variant_id', 'product_id'], 'unique_shopify_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_product_variants');
        Schema::rename('product_variants', 'product_combinations');
    }
}
