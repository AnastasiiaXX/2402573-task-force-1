<?php

use app\models\User;
use app\models\Category;
use app\models\Location;

$userIds = User::find()->select('id')->column();
$categoryIds = Category::find()->select('id')->column();
$locationIds = Location::find()->select('id')->column();

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'title' => $faker->sentence(),
    'description' => $faker->text(),
    'cost' => $faker->numberBetween(700, 7000),
    'date_add' => $faker->DateTimeThisYear()->format('Y-m-d H:i:s'),
    'date_end' => $faker->DateTimeBetween('now', '+1 month')->format('Y-m-d H:i:s'),
    'status' => $faker->randomElement(['new', 'in_progress', 'completed']),
    'employer_id' => $faker->randomElement($userIds),
    'worker_id' => $faker->optional()->randomElement($userIds),
    'location_id' => $faker->randomElement($locationIds),
    'category_id' => $faker->randomElement($categoryIds)
];
