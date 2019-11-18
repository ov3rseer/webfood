<?php

namespace console\fixtures;

class MenuFixture extends Fixture
{
    public $modelClass = 'common\models\reference\Menu';

    public $depends = [
        'console\fixtures\MealFixture',
        'console\fixtures\ComplexFixture',
    ];
}
