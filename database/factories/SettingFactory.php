<?php

use App\Models\User;
use App\Models\User\Setting;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/** @var Factory $factory */
$factory->define(Setting::class, function (Faker $faker) {
    return [
        'user_id' =>  factory(User::class)->create()->id,
        'key' => strtoupper($faker->word),
        'value' => $faker->word,
        'description' => $faker->text,
    ];
});
