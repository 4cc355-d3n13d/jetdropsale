<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertShopifyChargeSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->insert([
            ['key' => 'settings.shopify.charge.name', 'value' => 'Dropwow plan'],
            ['key' => 'settings.shopify.charge.price', 'value' => 10],
            ['key' => 'settings.shopify.charge.test_charges', 'value' => true],
        ]);
        cache()->forget(\App\Models\Setting::getCacheKey());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
