<?php

namespace console\fixtures;

class FatherFixture extends Fixture
{
    public $modelClass = 'common\models\reference\Father';

    public $depends = [
        'console\fixtures\UserFixture',
    ];
}