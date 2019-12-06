<?php

use console\fixtures\Fixture;
use common\models\reference\User;

/** @var Fixture $this */

$keys = ['provider-1'];

$objects = [
    'provider-1' => [
        'name' => 'ЕЦМЗ',
        'city' => 'Нижний Новгород',
        'zip_code' => '423800',
        'address' => 'ул. Авиаконструктора Яковлева, дом 1',
    ]
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
