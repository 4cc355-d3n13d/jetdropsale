<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnableInvoices extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->string('billing_reference')->nullable()->comment('Customer reference for payment gateways');
        });

        Schema::table('shopify_orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopify_orders', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['invoice_id']);
            }
            $table->dropColumn(['status', 'invoice_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['credit_limit','billing_reference']);
        });
    }
}
