<?php

namespace console\fixtures;

class MealFixture extends Fixture
{
    public $modelClass = 'common\models\reference\Meal';

    public $depends = [
        'console\fixtures\ProductFixture',
        'console\fixtures\MealCategoryFixture',
    ];
}