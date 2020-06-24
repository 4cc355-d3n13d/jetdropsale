<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIndexProductCategoriesTable extends Migration
{

    /**
    * Run the migrations.
    */
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->default(0)->index()->change();
        });
    }

    /**
    * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->default(0)->change();
        });
    }
}
