<?php

use common\models\enum\ComplexType;
use common\models\enum\FoodType;

return [
    'complex-1' => [
        'name' => 'Комплекс №1',
        'is_active' => true,
        'complex_type_id' => ComplexType::BREAKFAST,
        'food_type_id' => FoodType::BUFFET,
        'price' => 120.00,
        'description' => 'Комплекс для завтрака',
    ],
    'complex-2' => [
        'name' => 'Комплекс №2',
        'is_active' => true,
        'complex_type_id' => ComplexType::DINNER,
        'food_type_id' => FoodType::BUFFET,
        'price' => 120.00,
        'description' => 'Комплекс для обеда',
    ],
    'complex-3' => [
        'name' => 'Комплекс №3',
        'is_active' => true,
        'complex_type_id' => ComplexType::BREAKFAST,
        'food_type_id' => FoodType::BUFFET,
        'price' => 120.00,
        'description' => 'Комплекс для завтрака',
    ],
    'complex-4' => [
        'name' => 'Комплекс №4',
        'is_active' => true,
        'complex_type_id' => ComplexType::DINNER,
        'food_type_id' => FoodType::BUFFET,
        'price' => 150.00,
        'description' => 'Комплекс для обеда',
    ],
    'complex-5' => [
        'name' => 'Комплекс №5',
        'is_active' => true,
        'complex_type_id' => ComplexType::SUPPER,
        'food_type_id' => FoodType::BUFFET,
        'price' => 150.00,
        'description' => 'Комплекс для ужина',
    ],
];