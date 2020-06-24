<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopifyProductAddMyproductId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shopify_products', function (Blueprint $table) {
            $table->unsignedInteger('my_product_id')->nullable();
            $table->foreign('my_product_id')->references('id')->on('my_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }
        Schema::table('shopify_products', function (Blueprint $table) {
            $table->dropForeign(['my_product_id']);
        });
        Schema::table('shopify_products', function (Blueprint $table) {
            $table->dropColumn(['my_product_id']);
        });
    }
}
