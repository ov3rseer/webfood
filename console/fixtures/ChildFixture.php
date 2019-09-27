<?php

namespace console\fixtures;

class ChildFixture extends Fixture
{
    public $modelClass = 'common\models\reference\Child';

    public $depends = [
        'console\fixtures\ServiceObjectFixture',
        'console\fixtures\SchoolClassFixture',
        'console\fixtures\FatherFixture',
    ];
}