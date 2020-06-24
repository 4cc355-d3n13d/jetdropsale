<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterShopifyChargeNonuniqChargeId extends Migration
{
    public function up():void
    {
        Schema::table('shopify_charges', function (Blueprint $table) {
            $table->dropUnique('shopify_charges_charge_id_unique');
            $table->unsignedBigInteger('charge_id')->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
