<?php

use common\models\reference\Meal;
use common\models\reference\Product;

return [
    'meal-1#product-1' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-1')->primaryKey,
        'product_id' =>  $this->getFixtureModel(Product::class, 'product-5')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.3,
    ],
    'meal-1#product-2' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-1')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-1')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-2#product-2' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-2')->primaryKey,
        'product_id' =>  $this->getFixtureModel(Product::class, 'product-3')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.3,
    ],
    'meal-2#product-4' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-2')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-4')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-3#product-10' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-3')->primaryKey,
        'product_id' =>  $this->getFixtureModel(Product::class, 'product-10')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.3,
    ],
    'meal-3#product-7' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-3')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-7')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.3,
    ],
    'meal-4#product-11' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-4')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-11')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-4#product-12' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-4')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-12')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-5#product-13' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-5')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-13')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-6#product-14' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-6')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-14')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-7#product-15' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-7')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-15')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.25,
    ],
    'meal-7#product-16' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-7')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-16')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-8#product-17' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-8')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-17')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-8#product-10' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-8')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-10')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-9#product-1' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-9')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-1')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-9#product-2' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-9')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-2')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-9#product-3' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-9')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-3')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-9#product-10' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-9')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-10')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.2,
    ],
    'meal-10#product-3' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-10')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-3')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.2,
    ],
    'meal-10#product-18' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-10')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-18')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
    'meal-11#product-10' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-11')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-10')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.2,
    ],
    'meal-11#product-18' => [
        'parent_id' => $this->getFixtureModel(Meal::class, 'meal-11')->primaryKey,
        'product_id' => $this->getFixtureModel(Product::class, 'product-18')->primaryKey,
        'unit_id' => 2,
        'product_quantity' => 0.1,
    ],
];