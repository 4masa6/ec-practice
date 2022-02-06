<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->realText($maxNbChars = 20, $indexSize = 4),
        'description' => $faker->text,
        'price' => $faker->numberBetween(300, 30000),
        'category_id' => $faker->numberBetween(1, 20),
        'recommend_flag' => $faker->numberBetween(0, 1),
        'carriage_flag' => $faker->numberBetween(0, 1),
    ];
});
