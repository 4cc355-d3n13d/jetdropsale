<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMyProductsTable extends Migration
{
    public function up(): void
    {
        Schema::table('my_products', function (Blueprint $table) {
            $table->renameColumn('shopify_products_id', 'shopify_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('my_products', function (Blueprint $table) {
            $table->renameColumn('shopify_product_id', 'shopify_products_id');
        });
    }
}
