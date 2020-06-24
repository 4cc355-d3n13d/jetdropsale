<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOptionsCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->references('id')->on('products');
            $table->string('name');
            $table->string('value');
            $table->string('image');
            $table->unsignedInteger('ali_sku');
            $table->unsignedInteger('ali_option_id');
            $table->timestamps();
        });
        Schema::create('product_combinations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->references('id')->on('products');
            $table->string('sku')->unique();
            $table->unsignedInteger('amount');
            $table->decimal('price');
            $table->json('combination');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_combinations');
    }
}
