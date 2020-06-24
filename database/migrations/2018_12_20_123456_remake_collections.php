<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemakeCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('my_product_collections', 'my_product_has_collections');

        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->unsignedInteger('collection_id')->nullable()->after('title');
        });

        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->unsignedInteger('shopify_id')->nullable()->after('collection_id');
        });

        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        Schema::table('my_product_has_collections', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::create('my_collections', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('shopify_id')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['title']);
            $table->index(['shopify_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('my_collections');

        Schema::rename('my_product_has_collections', 'my_product_collections');

        Schema::table('my_product_collections', function (Blueprint $table) {
            $table->string('title')->nullable()->after('my_product_id');
        });
    }
}
