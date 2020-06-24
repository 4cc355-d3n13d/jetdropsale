<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyChargesTable extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_charges', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('charge_id')->unique();

            // Test mode or real
            $table->boolean('test');
            $table->string('status')->nullable();
            $table->string('name')->nullable();
            $table->string('terms')->nullable();


            $table->decimal('price', 8, 2);
            $table->decimal('capped_amount', 8, 2)->nullable();
            $table->integer('trial_days')->nullable();
            $table->timestamp('billing_on')->nullable();
            $table->timestamp('activated_on')->nullable();
            $table->timestamp('trial_ends_on')->nullable();
            $table->timestamp('cancelled_on')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->integer('shop_id')->unsigned();
            $table->foreign('shop_id')->references('id')->on('shopify_shops')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::drop('shopify_charges');
    }
}
