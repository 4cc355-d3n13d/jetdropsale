<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShipToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('description')->after('value')->nullable();
        });

        $s = \App\Models\Setting::create([
            'key' => \App\Models\ShipGoods::KEY,
            'value'=>'2.9'
        ]);

        \App\Models\OrderCart::where('goods_type', \App\Models\ShipGoods::class)->update(['goods_id'=>$s->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        \App\Models\ShipGoods::first()->delete();
    }
}
