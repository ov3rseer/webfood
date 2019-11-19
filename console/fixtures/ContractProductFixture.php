<?php

namespace console\fixtures;

class ContractProductFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\ContractProduct';

    public $depends = [
        'console\fixtures\ContractFixture',
        'console\fixtures\ProductFixture',
    ];
}