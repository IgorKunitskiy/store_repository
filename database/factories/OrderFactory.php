<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Order;
use App\Models\User ;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$users = null;

$factory->define(Order::class, function (Faker $faker) use (&$users) {
    $users = User::all();

    return [
        'user_id' => $faker->randomElement($users)->id,
        'status' => $faker->randomElement(Order::STATUSES),
    ];
});
