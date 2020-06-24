<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixShopifyIdBigInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_collections', function (Blueprint $table) {
            $table->dropColumn('shopify_id');
        });
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->dropColumn('shopify_id');
        });
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('shopify_collect_id')->nullable();
        });
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->renameColumn('collection_id', 'my_collection_id');
        });
        Schema::create('shopify_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('my_collection_id');
            $table->unsignedBigInteger('shopify_collection_id');
            $table->unsignedInteger('shop_id');
            $table->timestamps();
            $table->unique(['my_collection_id', 'shopify_collection_id', 'shop_id'], 'unique_collection_by_shop');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->renameColumn('my_collection_id', 'collection_id');
        });
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->unsignedInteger('shopify_id')->nullable();
        });
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->dropColumn('shopify_collect_id');
        });
        Schema::table('my_collections', function (Blueprint $table) {
            $table->unsignedInteger('shopify_id')->nullable();
        });
        Schema::drop('shopify_collections');
    }
}
