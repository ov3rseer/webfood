<?php

use common\models\reference\ProductCategory;

return [
    'product-1' => [
        'name' => 'Морковь',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::className(), 'product-category-1')->primaryKey,
        'product_code' => 1,
        'price' => 30.00,
        'unit_id' => 2,
    ],
    'product-2' => [
        'name' => 'Лук',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::className(), 'product-category-1')->primaryKey,
        'product_code' => 2,
        'price' => 45.00,
        'unit_id' => 2,
    ],
    'product-3' => [
        'name' => 'Картофель',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::className(), 'product-category-1')->primaryKey,
        'product_code' => 3,
        'price' => 25.30,
        'unit_id' => 2,
    ],
    'product-4' => [
        'name' => 'Укроп сушеный',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::className(), 'product-category-10')->primaryKey,
        'product_code' => 4,
        'price' => 200.99,
        'unit_id' => 2,
    ],
    'product-5' => [
        'name' => 'Кальмары замороженные',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::className(), 'product-category-8')->primaryKey,
        'product_code' => 5,
        'price' => 350.00,
        'unit_id' => 2,
    ],
    'product-6' => [
        'name' => 'Фарш говяжий',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::className(), 'product-category-3')->primaryKey,
        'product_code' => 6,
        'price' => 550.00,
        'unit_id' => 2,
    ],
];