<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexUserIdMyProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_products', function (Blueprint $table) {
            $table->index('user_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_products', function (Blueprint $table) {
            if (! app()->runningUnitTests()) {
                $table->dropIndex('user_id');
            }
        });
    }
}
