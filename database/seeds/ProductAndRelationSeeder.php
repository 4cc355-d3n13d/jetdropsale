<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductAndRelationSeeder extends Seeder
{
    public function run(): void
    {
        $count = (int) env('TEST_GENERATE_ITEM_COUNT');
        factory(\App\Models\Product\Product::class, $count)->create()->each(function (Product\Product $product) {
            $product->options()->save(factory(Product\ProductOption::class)->make());
            $product->combinations()->save(factory(Product\ProductVariant::class)->make());
            $product->options()->save(factory(Product\ProductOption::class)->make());
        });
    }
}
