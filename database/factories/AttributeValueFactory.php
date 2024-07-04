<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\AttributeValue;
use Faker\Generator as Faker;

$factory->define(AttributeValue::class, function (Faker $faker) {
    return [
        'element_id' => 1,
        'timestamp' => $this->faker->date('Y-m-d H:i:s'),
        'acetylene' => rand(1, 45),
        'ethylene' => rand(1, 100),
        'methane' => rand(1, 100),
        'ethane' => rand(1, 100),
        'hydrogen' => rand(1, 100),
        'oxygen' => rand(1, 100),
        'carbon_monoxide' => rand(1, 100),
        'carbon_dioxide' => rand(1, 100),
        'tdcg' => rand(1, 100),
        't1' => 'Normal',
        't2' => 'Normal',
        't3_biotemp' => 'Normal',
        't3_fr' => 'Normal',
        't3_midel' => 'Normal',
        't3_silicon' => 'Normal',
        't4' => 'Normal',
        't5' => 'Normal',
        't6' => 'Normal',
        't7' => 'Normal',
        'p1' => 'Normal',
        'p2' => 'Normal',
        'iec_ratio' => 'Normal',
        'dornenberg' => 'Normal',
        'rogers_ratio' => 'Normal',
        'carbon_ratio' => 'Test',
        'nei' => 'Test'
    ];
});
