<?php

use App\Models\Product\MyProduct;
use Illuminate\Database\Seeder;

class MyProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $count = env('TEST_GENERATE_ITEM_COUNT');
        factory(MyProduct::class, (int)$count)->create();
    }
}
