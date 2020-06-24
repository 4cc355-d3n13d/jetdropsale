<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopifyOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->string('origin')->after('user_id')->nullable();
        });
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->renameColumn('shopify_order_id', 'origin_id');
        });
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->smallInteger('origin_status')->after('origin_id')->nullable();
        });
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->dropColumn('shopify_status');
        });
        Schema::rename('shopify_orders', 'orders');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('origin_id', 'shopify_order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('origin_status', 'shopify_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('origin');
        });

        Schema::rename('orders', 'shopify_orders');
    }
}
