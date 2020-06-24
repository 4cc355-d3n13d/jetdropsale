<?php

use App\Models\Product\Product;
use Illuminate\Database\Seeder;

/**
 * Class ProductsTableSeeder
 */
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = env('TEST_GENERATE_ITEM_COUNT');
        factory(Product::class, (int)$count)->create();
    }
}
