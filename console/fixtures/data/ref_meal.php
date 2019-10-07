<?php

use common\models\enum\FoodType;
use common\models\reference\MealCategory;

return [
    'meal-1' => [
        'name' => 'Кальмары в остро-сладком соусе',
        'is_active' => true,
        'meal_category_id' => $this->getFixtureModel(MealCategory::className(), 'meal-category-1')->primaryKey,
        'food_type_id' => FoodType::BUFFET,
        'price' => 108.00,
        'description' => 'Вкуснотень',
    ],
    'meal-2' => [
        'name' => 'Жареная картошечка с укропчиком',
        'is_active' => true,
        'meal_category_id' => $this->getFixtureModel(MealCategory::className(), 'meal-category-1')->primaryKey,
        'food_type_id' => FoodType::BUFFET,
        'price' => 108.00,
        'description' => 'Вкуснотень',
    ],
];