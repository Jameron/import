<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
$factory->defineAs(Jameron\Regulator\Models\Permission::class, 'upload_csv', function (Faker\Generator $faker) {
    return [
        'slug' => 'upload_csv',
        'name' => 'Upload CSV',
    ];
});
