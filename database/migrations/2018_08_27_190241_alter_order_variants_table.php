<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrderVariantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->json('product_variants')->nullable()->after('shopify_status');
            $table->json('billing_address')->nullable()->after('shopify_status');
            $table->json('shipping_address')->nullable()->after('shopify_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->dropColumn(['product_variants','billing_address','shipping_address']);
        });
    }
}
