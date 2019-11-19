<?php

namespace console\fixtures;

class ServiceObjectContractFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\ServiceObjectContract';

    public $depends = [
        'console\fixtures\ServiceObjectFixture',
        'console\fixtures\ContractFixture',
    ];
}