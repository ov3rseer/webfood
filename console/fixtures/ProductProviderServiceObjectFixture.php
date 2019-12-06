<?php

namespace console\fixtures;

class ProductProviderServiceObjectFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\ProductProviderServiceObject';

    public $depends = [
        'console\fixtures\ServiceObjectFixture',
        'console\fixtures\ProductProviderFixture',
    ];
}