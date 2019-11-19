<?php

use common\models\reference\Contract;
use common\models\reference\ServiceObject;

return [
    'object-1#contract-1' => [
        'parent_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'contract_id' => $this->getFixtureModel(Contract::class, 'contract-1')->primaryKey,
        'address' => '603043, Нижегородская обл, Нижний Новгород г, Лоскутова ул, дом № 13',
    ],
];