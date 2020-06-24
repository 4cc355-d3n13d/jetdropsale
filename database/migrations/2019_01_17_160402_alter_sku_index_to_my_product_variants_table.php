<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSkuIndexToMyProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_product_variants', function (Blueprint $table) {
            $table->index('sku', 'my_product_variants_sku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_product_variants', function (Blueprint $table) {
            $table->dropIndex('my_product_variants_sku');
        });
    }
}
