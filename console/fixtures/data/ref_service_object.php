<?php

use common\models\enum\ServiceObjectType;
use common\models\reference\User;
use console\fixtures\Fixture;

/** @var Fixture $this */

$keys = ['object-1', 'object-2'];

$objects = [
    'object-1' => [
        'name' => 'Школа №23',
        'service_object_type_id' => ServiceObjectType::SCHOOL,
        'city' => 'Нижний Новгород',
        'zip_code'=> '652130',
        'address'=> 'пер. 1-й Коптельский, дом 59',
    ],
    'object-2' => [
        'name' => 'Детский сад "Колобок"',
        'service_object_type_id' => ServiceObjectType::KINDERGARTEN,
        'city' => 'Нижний Новгород',
        'zip_code'=> '307460',
        'address'=> 'ул. Бабаевская, дом 9',
    ],
];

foreach ($keys as $key) {
    $result[$key] = [
        'is_active' => true,
        'user_id' => $this->getFixtureModel(User::class, 'user-' . $key)->primaryKey
    ];
    $result[$key] = array_merge($objects[$key], $result[$key]);

    $user = User::findOne(['id' => $this->getFixtureModel(User::class, 'user-' . $key)->primaryKey]);
    $user->name_full = $objects[$key]['name'];
    $user->save();
}

return $result;

