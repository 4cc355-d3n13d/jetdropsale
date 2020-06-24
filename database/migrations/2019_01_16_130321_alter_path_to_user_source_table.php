<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPathToUserSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_sources', function (Blueprint $table) {
            $table->string('path')->after('user_id')->nullable();
        });

        Schema::table('user_sources', function (Blueprint $table) {
            $table->text('full_url')->after('ip')->nullable();
        });

        Schema::table('user_sources', function (Blueprint $table) {
            $table->text('http_referrer_full')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_sources', function (Blueprint $table) {
            $table->dropColumn('path');
            $table->dropColumn('full_url');
        });
    }
}
