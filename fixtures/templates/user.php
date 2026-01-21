<?php

use app\models\Specialty;
use app\models\Location;

$locationIds = Location::find()->select('id')->column();
$specialtyIds = Specialty::find()->select('id')->column();
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->name,
    'password' => password_hash('123456', PASSWORD_DEFAULT),
    'email' => $faker->email,
    'role' => $faker->randomElement(['customer', 'worker']),
    'birthday' => $faker->date('Y-m-d'),
    'avatar' => null,
    'phone_number' => $faker->numerify('79#########'),
    'telegram_name' => $faker->userName,
    'about' => $faker->paragraph,
    'location_id' => $faker->randomElement($locationIds),
    'specialty_id' => !empty($specialtyIds) ? $faker->randomElement($specialtyIds) : null,
];
