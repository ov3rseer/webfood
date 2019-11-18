<?php

namespace console\fixtures;

class MenuMealFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\MenuMeal';

    public $depends = [
        'console\fixtures\MenuFixture',
        'console\fixtures\MealFixture',
    ];
}