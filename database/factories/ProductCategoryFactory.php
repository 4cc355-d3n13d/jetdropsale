<?php

use App\Models\Product\Category;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Category::class, function (Faker $faker) {
    $title = 'Category-' . random_int(0, 8). random_int(0, 8);

    return [
        'title' => $title,
        'ali_title' => $title,
        'slug' => $title,
        'parent_id' => 0,
        'icon' => '',
        'ali_id' => random_int(1147483647, 2147483647),
        'created_at' => $faker->dateTimeBetween('-5 day', '+5 day'),
        'updated_at' => $faker->dateTimeBetween('-2 day', '+2 day'),
    ];
});
