<?php

use common\models\enum\ServiceObjectType;
use common\models\reference\User;
use console\fixtures\Fixture;

/** @var Fixture $this */
/** @noinspection PhpUnhandledExceptionInspection */

return [
    'object-1' => [
        'is_active' => true,
        'name' => 'Частный лицей N777 с уклоном в экономику',
        'service_object_code' => '01',
        'service_object_type_id' => ServiceObjectType::SCHOOL,
        'user_id' => $this->getFixtureModel(User::className(), 'user-object-1')->primaryKey
    ],
    'object-2' => [
        'is_active' => true,
        'name' => 'Детский сад',
        'service_object_code' => '02',
        'service_object_type_id' => ServiceObjectType::KINDERGARTEN,
        'user_id' => $this->getFixtureModel(User::className(), 'user-object-2')->primaryKey
    ],
];