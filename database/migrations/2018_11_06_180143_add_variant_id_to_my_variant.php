<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariantIdToMyVariant extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('my_product_variants', function (Blueprint $table) {
            $table->unsignedInteger('product_variant_id')->nullable()->after('my_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('my_product_variants', function (Blueprint $table) {
            $table->dropColumn('product_variant_id')->nullable();
        });
    }
}
