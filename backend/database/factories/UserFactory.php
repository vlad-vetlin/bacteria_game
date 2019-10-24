<?php

/** @var Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'rating' => $faker->numberBetween(0, 5000),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'description' => $faker->realText(),
        'password' => Hash::make('secret'),
        'email' => $faker->email,
        'country' => $faker->country,
        'city' => $faker->city,
        'is_admin' => 0,
    ];
});
