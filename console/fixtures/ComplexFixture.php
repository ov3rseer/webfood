<?php

namespace console\fixtures;

class ComplexFixture extends Fixture
{
    public $modelClass = 'common\models\reference\Complex';

    public $depends = [
        'console\fixtures\MealFixture',
    ];
}