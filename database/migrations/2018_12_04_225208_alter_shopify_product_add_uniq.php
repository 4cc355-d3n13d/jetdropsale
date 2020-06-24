<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopifyProductAddUniq extends Migration
{
    public function up(): void
    {
        Schema::table('shopify_products', function (Blueprint $table) {
            $table->unique(['user_id', 'shopify_id']);
        });
    }

    public function down(): void
    {
    }
}
