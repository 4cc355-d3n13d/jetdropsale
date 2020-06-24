<?php

use App\Enums\MyProductStatusType;
use App\Models\Product\Product;
use App\Models\Shopify\ProductVariant;
use App\Models\Shopify\ShopifyProduct;
use App\Models\Product\MyProduct;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use App\Models\Product\MyProductVariant;

/** @var Factory $factory */
$factory->define(ShopifyProduct::class, function (Faker $faker) {
    $product = factory(Product::class)->create();
    $myProduct = factory(MyProduct::class)->create([
        'product_id' => $product->id,
        'status' => MyProductStatusType::CONNECTED,
    ]);

    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'product_id' => $product->id,
        'my_product_id' => $myProduct->id,
        'combination_id' => null,
        'created_at' => $faker->dateTimeBetween('-5 day', '+5 day'),
        'updated_at' => $faker->dateTimeBetween('-2 day', '+2 day'),
    ];
});

$factory->define(ProductVariant::class, function () {
    $myProduct = factory(MyProduct::class)->create();

    return [
        'product_id' => $myProduct->product_id,
        'product_variant_id' => factory(MyProductVariant::class, ['my_product_id' => $myProduct->id])->create(),
        'shopify_variant_id' => mt_rand(1, 100000),
    ];
});
