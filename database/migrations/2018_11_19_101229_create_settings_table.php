<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string("key")->unique();
            $table->string("value");
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'admitad.ali.parse.api', 'value' => 'https://iapi.admitad.com/v1/ali_products/'],
            ['key' => 'admitad.ali.parse.token', 'value' => 'bcdb89039265e7d71c7b01d9a4794a3a4936a5de'],
            ['key' => 'admitad.ali.parse.limit', 'value' => '100'],
            ['key' => 'admitad.ali.parse.all_webmasters', 'value' => '1'],
            ['key' => 'admitad.ali.parse.data_interval', 'value' => '10'],
            ['key' => 'admitad.ali.parse.update_interval', 'value' => '10']
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
        Schema::dropIfExists('settings');
    }
}
