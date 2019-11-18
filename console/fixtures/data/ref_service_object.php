<?php

use common\models\enum\ServiceObjectType;
use common\models\reference\User;
use console\fixtures\Fixture;

/** @var Fixture $this */
/** @noinspection PhpUnhandledExceptionInspection */

$keys = ['object-1', 'object-2'];

$objects = [
    'object-1' => [
        'name' => 'Школа №23',
        'service_object_code' => '01',
        'service_object_type_id' => ServiceObjectType::SCHOOL,
    ],
    'object-2' => [
        'name' => 'Детский сад "Колобок"',
        'service_object_code' => '02',
        'service_object_type_id' => ServiceObjectType::KINDERGARTEN,
    ],
];

foreach ($keys as $key) {
    $result[$key] = [
        'is_active' => true,
        'user_id' => $this->getFixtureModel(User::class, 'user-' . $key)->primaryKey
    ];
    $result[$key] = $objects[$key];

    $user = User::findOne(['id' => $this->getFixtureModel(User::class, 'user-' . $key)->primaryKey]);
    $user->name_full = $objects[$key]['name'];
    $user->save();
}

return $result;

