<?php

namespace console\fixtures;

class ProductFixture extends Fixture
{
    public $modelClass = 'common\models\reference\Product';

    public $depends = [
        'console\fixtures\ProductCategoryFixture',
    ];
}