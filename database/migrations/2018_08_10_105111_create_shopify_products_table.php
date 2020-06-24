<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopify_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('shopify_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedBigInteger('combination_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->integer('status')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('shopify_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!app()->runningUnitTests()) {
            Schema::table('shopify_products', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropForeign(['user_id']);
            });
        }

        Schema::dropIfExists('shopify_products');
    }
}
