<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PromoCode;
use Faker\Generator as Faker;

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

$factory->define(PromoCode::class, function (Faker $faker) {
    return [
        'code' => (new PromoCode)->generateCode(),
        'radius' => 1000,
        'amount' => 500.50,
        'expires_at' => date('Y-m-d', strtotime("+3 days"))
    ];
});
