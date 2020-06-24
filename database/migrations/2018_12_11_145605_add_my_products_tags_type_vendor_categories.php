<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMyProductsTagsTypeVendorCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_products', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('user_id');
            $table->string('type')->nullable()->after('user_id');
        });

        Schema::create('my_product_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('my_product_id');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('my_product_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('my_product_id');
            $table->string('title');
            $table->timestamps();
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
            $table->dropColumn('vendor');
        });
        Schema::table('my_products', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::drop('my_product_tags');
        Schema::drop('my_product_collections');
    }
}
