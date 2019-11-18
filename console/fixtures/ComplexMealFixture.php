<?php

namespace console\fixtures;

class ComplexMealFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\ComplexMeal';

    public $depends = [
        'console\fixtures\MealFixture',
        'console\fixtures\ComplexFixture',
    ];
}