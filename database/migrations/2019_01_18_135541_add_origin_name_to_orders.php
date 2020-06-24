<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginNameToOrders extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('origin_name')->nullable()->after('origin_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('origin_name', 'origin_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('origin_name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('origin_name');
        });
    }
}
