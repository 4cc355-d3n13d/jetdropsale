<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyProductOptionsVariantsTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('my_product_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('my_product_id')->nullable();
            $table->string('name');
            $table->string('value');
            $table->string('image');
            $table->unsignedInteger('ali_sku');
            $table->unsignedInteger('ali_option_id');
            $table->timestamps();
        });

        Schema::create('my_product_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('my_product_id')->nullable();
            $table->string('sku');
            $table->unsignedInteger('amount');
            $table->decimal('price');
            $table->json('combination');
            $table->timestamps();
        });

        Schema::table('my_product_options', function (Blueprint $table) {
            $table->foreign('my_product_id')->references('id')->on('my_products');
        });

        Schema::table('my_product_variants', function (Blueprint $table) {
            $table->foreign('my_product_id')->references('id')->on('my_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_product_options');
        Schema::dropIfExists('my_product_variants');
    }
}
