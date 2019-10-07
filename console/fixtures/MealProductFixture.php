<?php

namespace console\fixtures;

class MealProductFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\MealProduct';

    public $depends = [
        'console\fixtures\MealCategoryFixture',
        'console\fixtures\MealFixture',
    ];
}