<?php

namespace console\fixtures;

class MenuComplexFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\MenuComplex';

    public $depends = [
        'console\fixtures\MenuFixture',
        'console\fixtures\ComplexFixture',
    ];
}