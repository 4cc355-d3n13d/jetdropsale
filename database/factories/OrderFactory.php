<?php

use App\Enums\OrderOriginType;
use App\Enums\ShopifyStatusType;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Shopify;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Order::class, function (Faker $faker) {
    $user = factory(User::class)->create();

    return [
        'origin' => OrderOriginType::SHOPIFY,
        'origin_id' => mt_rand(1, 1000),
        'origin_status' => ShopifyStatusType::PARTIALLY_PAID,
        'user_id' => function () use ($user) {
            return $user->id;
        },
        'shop_id' => function () use ($user, $faker) {
            return factory(Shopify\Shop::class)->create([
                'user_id' => $user,
                'shop' => "{$faker->name}.myshopify.com",
            ])->id;
        },
        'billing_address' => null,
        'product_variants' => '',
        'shipping_address' => [
            'name' => $faker->name,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'address1' => $faker->address,
            'address2' => '',
            'phone' => $faker->phoneNumber,
            'zip' => $faker->postcode,
            'city' => $faker->city,
            'country' => $faker->company,
            'province' => $faker->citySuffix,
            'company' => null,
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'country_code' => $faker->countryCode,
            'province_code' => '',
        ]
    ];
});


/** @var Factory $factory */
$factory->define(OrderCart::class, function (Faker $faker) {
    return [
        'order_id' => null,
        'goods_type' => 'App\Models\ShipGoods',
        'goods_id' => 10,
        'title' => 'Ship cost',
        'image' => null,
        'price' => 4.80,
        'amount' => mt_rand(1, 5),
    ];
});
