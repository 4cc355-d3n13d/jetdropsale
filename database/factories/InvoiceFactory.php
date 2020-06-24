<?php

use App\Models\Invoice;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'total_sum' => rand(1, 100)
    ];
});
