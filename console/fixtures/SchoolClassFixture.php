<?php

namespace console\fixtures;

class SchoolClassFixture extends Fixture
{
    public $modelClass = 'common\models\reference\SchoolClass';

    public $depends = [
        'console\fixtures\ServiceObjectFixture',
    ];
}