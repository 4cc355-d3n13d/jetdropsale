<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceContent extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->json('content')->nullable();
        });
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->decimal('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('content');
        });
        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}
