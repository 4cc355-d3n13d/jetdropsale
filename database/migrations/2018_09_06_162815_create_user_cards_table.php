<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCardsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->increments('id')->comment('Internal card identificator');
            $table->unsignedInteger('user_id')->comment('Card owner user identificator for relation');
            $table->boolean('user_default')->default(false);
            $table->string('billing_reference')->comment('Source reference for payment gateways');
            $table->string('brand')->comment('Card brand');
            $table->unsignedSmallInteger('last4')->comment('Last 4 digits of card number');
            $table->unsignedTinyInteger('exp_month')->comment('Card expire month');
            $table->unsignedSmallInteger('exp_year')->comment('Card expire year');
            $table->timestamps();
        });

        Schema::table('user_cards', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cards');
    }
}
