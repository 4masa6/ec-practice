<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\Review;
use App\User;
use Faker\Generator as Faker;

$factory->define(Review::class, function (Faker $faker) {
    return [
        'content' => $faker->realText($maxNbChars = 50, $indexSize = 2),
        'product_id' => $faker->numberBetween(1, 30),
        'user_id' => $faker->numberBetween(1, 70),
    ];
});
