<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Product;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->domainWord,
    ];
});
