<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMyproductShopifyProductRemoveDublicates extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }
        //create temporary table
        DB::statement('CREATE table shopify_products_tmp SELECT * from shopify_products limit 0 ');
        DB::statement('INSERT INTO shopify_products_tmp SELECT sp1.* from shopify_products sp1 join (
                      SELECT min(id) id FROM shopify_products GROUP BY shopify_id
                      ) sp2 USING (id);');

        //drop foreign keys that prevent table deletion
        DB::statement('ALTER TABLE shopify_products DROP FOREIGN KEY shopify_products_my_product_id_foreign ');
        DB::statement('ALTER TABLE shopify_products DROP FOREIGN KEY shopify_products_product_id_foreign ');
        DB::statement('ALTER TABLE shopify_products DROP FOREIGN KEY shopify_products_user_id_foreign ');
        DB::statement('ALTER TABLE my_products DROP FOREIGN KEY shopify_products_id ');

        //rename temporary table back to original name
        DB::statement('DROP TABLE shopify_products');
        DB::statement('ALTER TABLE shopify_products_tmp RENAME TO shopify_products ');

        //create temporary table
        DB::statement('CREATE table my_products_tmp SELECT * from my_products limit 0 ');
        DB::statement('INSERT INTO my_products_tmp SELECT mp1.* from my_products mp1 join (
                      SELECT min(id) id FROM my_products GROUP BY user_id, product_id
                      ) sp2 USING (id)');

        DB::statement('ALTER TABLE my_product_options DROP FOREIGN KEY my_product_options_my_product_id_foreign ');
        DB::statement('ALTER TABLE my_product_variants DROP FOREIGN KEY my_product_variants_my_product_id_foreign ');

        DB::statement('DROP TABLE my_products ');
        DB::statement('ALTER TABLE my_products_tmp RENAME TO my_products ');

        DB::statement('ALTER TABLE my_products ADD PRIMARY KEY (id) ');
        DB::statement('ALTER TABLE my_products MODIFY id INT(10) unsigned NOT NULL AUTO_INCREMENT ');
        DB::statement('CREATE UNIQUE INDEX my_products_id_uindex ON my_products (id) ');

        Schema::table('shopify_products', function (Blueprint $table) {
            $table->primary('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('my_product_id')->references('id')->on('my_products');
            $table->foreign('product_id')->references('id')->on('products');
        });


        //delete row from related tables after primary table duplicated deletion.
        DB::statement('DELETE my_product_options from my_product_options left JOIN my_products
            ON my_product_options.my_product_id = my_products.id WHERE my_products.id is null');

        DB::statement('DELETE my_product_variants from my_product_variants left JOIN my_products
            ON my_product_variants.my_product_id = my_products.id WHERE my_products.id is null');

        Schema::table('my_product_options', function (Blueprint $table) {
            $table->foreign('my_product_id')->references('id')->on('my_products');
        });

        Schema::table('my_product_variants', function (Blueprint $table) {
            $table->foreign('my_product_id')->references('id')->on('my_products');
        });
    }

    public function down(): void
    {
        //
    }
}
