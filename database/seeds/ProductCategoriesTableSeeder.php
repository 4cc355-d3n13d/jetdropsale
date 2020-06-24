<?php

use App\Models\Product\Category;
use Illuminate\Database\Seeder;

/**
 * Class ProductCategoriesTableSeeder
 */
class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = env('TEST_GENERATE_ITEM_COUNT');
        factory(Category::class, (int)$count)->create();
    }
}
