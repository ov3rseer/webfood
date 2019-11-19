<?php

use common\models\reference\Complex;
use common\models\reference\Meal;

return [
    'complex-1#meal-9' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-1')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-9')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-1#meal-2' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-1')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-2')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-1#meal-6' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-1')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-6')->primaryKey,
        'meal_quantity' => 1,
    ],

    'complex-2#meal-8' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-2')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-8')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-2#meal-7' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-2')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-7')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-2#meal-4' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-2')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-4')->primaryKey,
        'meal_quantity' => 1,
    ],

    'complex-3#meal-5' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-3')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-1')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-3#meal-9' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-3')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-4')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-3#meal-3' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-3')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-8')->primaryKey,
        'meal_quantity' => 1,
    ],

    'complex-4#meal-8' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-4')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-4')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-4#meal-7' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-4')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-7')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-4#meal-4' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-4')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-8')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-4#meal-11' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-4')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-11')->primaryKey,
        'meal_quantity' => 1,
    ],

    'complex-5#meal-5' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-5')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-5')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-5#meal-9' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-5')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-9')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-5#meal-3' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-5')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-3')->primaryKey,
        'meal_quantity' => 1,
    ],
    'complex-5#meal-10' => [
        'parent_id' => $this->getFixtureModel(Complex::class, 'complex-5')->primaryKey,
        'meal_id' => $this->getFixtureModel(Meal::class, 'meal-10')->primaryKey,
        'meal_quantity' => 1,
    ],
];