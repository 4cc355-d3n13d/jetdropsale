<?php

use App\Models\Card;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Card::class, function (Faker $faker) {
    $brands = ['Visa', 'MasterCard', 'American Express', 'Diners Club', 'JCB', 'Discover'];
    return [
        'last4' => mt_rand(1000, 9999),
        'brand' => $brands[array_rand($brands)],
        'primary' => false,
        'exp_month' => date('m'),
        'exp_year' => date('Y'),
        'billing_reference' => 'src_' . str_random(),
    ];
});
