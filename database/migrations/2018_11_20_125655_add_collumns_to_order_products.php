<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCollumnsToOrderProducts extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('vendor_order_id')->nullable()->after('quantity');
            $table->string('tracking_number')->nullable()->after('vendor_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('tracking_number');
        });
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('vendor_order_id');
        });
    }
}
