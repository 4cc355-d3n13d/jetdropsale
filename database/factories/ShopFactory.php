<?php

use App\Models\Shopify\Shop;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Shop::class, function (Faker $faker) {
    return [
        'shop' => strtolower($faker->streetName) . '.myshopify.com',
        'user_id' => factory(User::class)->create()->id,
        'access_token' => str_random(255),
        'status' => random_int(0, 32767),
    ];
});
