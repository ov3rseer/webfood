<?php

namespace console\fixtures;

class ServiceObjectFixture extends Fixture
{
    public $modelClass = 'common\models\reference\ServiceObject';

    public $depends = [
        'console\fixtures\UserFixture',
    ];
}