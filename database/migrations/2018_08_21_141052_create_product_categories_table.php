<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->default(0);
            $table->unsignedInteger('ali_id')->nullable()->unique();
            $table->string('title');
            $table->string('ali_title')->nullable();
            $table->string('slug');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable()->after('title');
            $table->foreign('category_id', 'category_id')->references('id')->on('product_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign('category_id');
            }
            $table->dropColumn(['category_id']);
        });
        Schema::dropIfExists('product_categories');
    }
}
