<?php

use App\Models\Shopify\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class FixturesSeeder
 */
class FixturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        factory(Shop::class)->create();
    }
}
