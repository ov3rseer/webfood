<?php

use common\models\reference\ProductCategory;

return [
    'product-1' => [
        'name' => 'Морковь',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-1')->primaryKey,
        'product_code' => 1,
        'price' => 30.00,
        'unit_id' => 2,
    ],
    'product-2' => [
        'name' => 'Лук',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-1')->primaryKey,
        'product_code' => 2,
        'price' => 45.00,
        'unit_id' => 2,
    ],
    'product-3' => [
        'name' => 'Картофель',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-1')->primaryKey,
        'product_code' => 3,
        'price' => 25.30,
        'unit_id' => 2,
    ],
    'product-4' => [
        'name' => 'Укроп сушеный',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-10')->primaryKey,
        'product_code' => 4,
        'price' => 200.99,
        'unit_id' => 2,
    ],
    'product-5' => [
        'name' => 'Кальмары замороженные',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-8')->primaryKey,
        'product_code' => 5,
        'price' => 350.00,
        'unit_id' => 2,
    ],
    'product-6' => [
        'name' => 'Фарш говяжий',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-3')->primaryKey,
        'product_code' => 6,
        'price' => 550.00,
        'unit_id' => 2,
    ],
    'product-7' => [
        'name' => 'Вырезка свиная',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-3')->primaryKey,
        'product_code' => 7,
        'price' => 500.00,
        'unit_id' => 2,
    ],
    'product-8' => [
        'name' => 'Минтай свежемороженный',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-3')->primaryKey,
        'product_code' => 8,
        'price' => 300.00,
        'unit_id' => 2,
    ],
    'product-9' => [
        'name' => 'Свекла',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-1')->primaryKey,
        'product_code' => 9,
        'price' => 70.50,
        'unit_id' => 2,
    ],
    'product-10' => [
        'name' => 'Капуста',
        'is_active' => true,
        'product_category_id' => $this->getFixtureModel(ProductCategory::class, 'product-category-1')->primaryKey,
        'product_code' => 10,
        'price' => 150.25,
        'unit_id' => 2,
    ],
];