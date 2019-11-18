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
];