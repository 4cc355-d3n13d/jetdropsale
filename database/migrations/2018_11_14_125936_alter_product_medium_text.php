<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductMediumText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->mediumText('description')->nullable()->change();
        });

        Schema::table('my_products', function (Blueprint $table) {
            $table->mediumText('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('my_products', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }
}
