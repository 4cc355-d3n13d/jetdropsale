<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPayed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('payed_at', 'paid_at');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('payed_with_card_id', 'paid_with_card_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('paid_at', 'payed_at');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('paid_with_card_id', 'payed_with_card_id');
        });
    }
}
