<?php

use App\Models\Product\Product;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use App\Models\Product\Category;

/** @var Factory $factory */
$factory->define(Product::class, function (Faker $faker) {
    $products = [
        ['Product-1', 1.20, 12, 'https://ae01.alicdn.com/kf/HTB1YkcYekfb_uJkHFJHq6z4vFXaA.jpg'],
        ['Product-2', 0.90, 44, 'https://ae01.alicdn.com/kf/UTB8mPzmsmnEXKJk43Ubq6zLppXay.jpg'],
        ['Product-3', 11.0, 321, 'https://ae01.alicdn.com/kf/UTB8KWSfgFfJXKJkSamHq6zLyVXa8.jpg'],
        ['Product-4', 1.30, 19, 'https://ae01.alicdn.com/kf/HTB1yhH5SFXXXXXSXFXXq6xXFXXXq.jpg'],
        ['Product-5', 18.3, 2, 'https://ae01.alicdn.com/kf/HTB1w0tsdAfb_uJkHFrdq6x2IVXaq.jpg']
    ];

    return [
        'title' => $products[random_int(0, 4)][0],
        'price' => $products[random_int(0, 4)][1],
        'image' => $products[random_int(0, 4)][3],
        'images' => json_encode([$products[random_int(0, 4)][3]]),
        'amount' => $products[random_int(0, 4)][2],
        'ali_id' => random_int(1147483647, 2147483647),
        //'created_at' => $faker->dateTimeBetween('-5 day', '+5 day'),
        //'updated_at' => $faker->dateTimeBetween('-2 day', '+2 day'),
        'description' => 'test description',
        'category_id' => factory(Category::class)->create()
    ];
});


/** @var Factory $factory */
$factory->define(ProductVariant::class, function (Faker $faker) {
    $combinations = [
        '{"1": "350262", "2": "29"}',
        '{"3": "175"}',
        '{"4": "361386", "5": "200004891"}',
        '{"6": "193", "7": "201336100"}',
        '{"8": "4182", "9": "350850"}',
        '{"10": "350853"}',
    ];

    return [
        'sku' => random_int(0, 2147483647),
        'amount' => random_int(0, 2000),
        'price' => random_int(0, 10000),
        'combination' => $combinations[random_int(0, 5)],
        'created_at' => $faker->dateTimeBetween('-5 day', '+5 day'),
        'updated_at' => $faker->dateTimeBetween('-2 day', '+2 day'),
        'product_id' => factory(Product::class)->create()->id
    ];
});


/** @var Factory $factory */
$factory->define(ProductDetail::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'value' => mt_rand(10000, 99999),
        'created_at' => $faker->dateTimeBetween('-5 day', '+5 day'),
        'updated_at' => $faker->dateTimeBetween('-2 day', '+2 day'),
        'product_id' => factory(Product::class)->create()->id
    ];
});


/** @var Factory $factory */
$factory->define(ProductOption::class, function (Faker $faker) {
    $optionNames = [
        'Option-1',
        'Option-2',
        'Option-3',
    ];
    $optionValues = [
        'value-1',
        'value-2',
        'value-3',
        'value-11',
        'value-22',
        'value-33',
    ];
    $aliSkus = [
        '350262',
        '29',
        '175',
        '193',
        '4182',
        '361386',
        '201336100',
        '200004891',
        '350850',
        '350853',
    ];
    $optionImages = [
        'https://ae01.alicdn.com/kf/HTB1AJaERFXXXXatXVXXq6xXFXXX6/New-Men-Business-Belt-2017-Cowhide-Genuine-Leather-Belts-For-Men-Luxury-Automatic-Buckle-Belts-Black.jpg',
        'https://ae01.alicdn.com/kf/HTB1OI2gieuSBuNjy1Xcq6AYjFXa9/Modyle-Smooth-Surface-Women-s-316L-Stainless-Steel-Ring-Trendy-Unique-Design-Female-Rings-New-Arrival.jpg',
        'https://ae01.alicdn.com/kf/HTB1mbY4OVXXXXb1XXXXq6xXFXXXC/1pair-Charm-LED-Earring-Light-Up-Crown-Glowing-Crystal-Stainless-Ear-Drop-Ear-Stud-Earring-Jewelry.jpg',
        'https://ae01.alicdn.com/kf/HTB1D4tGmXooBKNjSZFPq6xa2XXa6/15mm-x-7m-Cute-Ancient-Fountain-Ink-Painting-Decorative-Adhesive-Washi-Tape-Diy-Scrapbooking-Masking-Tape.jpg',
        'https://ae01.alicdn.com/kf/HTB1CNw1RXXXXXaKXXXXq6xXFXXXp/Meajoe-Trendy-Sexy-Punk-Gothic-Leather-Heart-Studded-Choker-Necklace-Vintage-Charm-Round-Collar-Necklaces-Women.jpg',
        'https://ae01.alicdn.com/kf/HTB11V59RpXXXXbhXpXXq6xXFXXXL/Patezim-Women-Cat-Eye-Sunglasses-Ladies-Vintage-Fashionable-Driving-Goggles-Sun-Glasses-For-Women-UV400-lens.jpg'
    ];

    return [
        'name' => $optionNames[random_int(0, 2)],
        'value' => $optionValues[random_int(0, 5)],
        'image' => $optionImages[random_int(0, 5)],
        'ali_sku' => $aliSkus[random_int(0, 9)],
        'ali_option_id' => random_int(0, 2147483647),
        'created_at' => $faker->dateTimeBetween('-5 day', '+5 day'),
        'updated_at' => $faker->dateTimeBetween('-2 day', '+2 day'),
    ];
});
