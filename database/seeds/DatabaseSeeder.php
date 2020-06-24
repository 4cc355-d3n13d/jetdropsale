<?php

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
             FixturesSeeder::class,
             ProductCategoriesTableSeeder::class,
             ProductAndRelationSeeder::class,
             MyProductsTableSeeder::class,
        ]);
    }
}
