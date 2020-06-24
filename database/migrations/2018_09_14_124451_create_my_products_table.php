<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('my_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->smallInteger('status')->default(0);
            $table->decimal('price')->default(0);
            $table->integer('amount')->default(0);
            $table->text('description');
            $table->string('image');
            $table->json('images');
            $table->unsignedBigInteger('ali_id')->nullable();

            $table->unsignedInteger('product_id')->nullable();
            $table->foreign('product_id', 'product_id')->references('id')->on('products');

            $table->unsignedInteger('shopify_products_id')->nullable();
            $table->foreign('shopify_products_id', 'shopify_products_id')->references('id')->on('shopify_products');

            $table->unsignedInteger('product_categories_id')->nullable();
            $table->foreign('product_categories_id', 'product_categories_id')->references('id')->on('product_categories');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id', 'user_id')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDefaultConnection() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        Schema::dropIfExists('my_products');
    }
}
