<?php

namespace console\fixtures;

class ProductProviderFixture extends Fixture
{
    public $modelClass = 'common\models\reference\ProductProvider';

    public $depends = [
        'console\fixtures\UserFixture',
    ];
}