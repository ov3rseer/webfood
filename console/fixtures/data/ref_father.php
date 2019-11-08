<?php

use common\models\reference\User;
use console\fixtures\Fixture;

/** @var Fixture $this */
/** @noinspection PhpUnhandledExceptionInspection */

$keys = ['father-1', 'father-2'];

$fathers['father-1'] = [
    'surname' => 'Пупкин',
    'forename' => 'Иван',
    'patronymic' => 'Иванович'
];

$fathers['father-2'] = [
    'surname' => 'Уткин',
    'forename' => 'Виктор',
    'patronymic' => 'Викторович',
];

$result = [];
foreach ($keys as $key) {
    $result[$key] = [
        'is_active' => true,
        'name' => $fathers[$key]['surname'] . ' '
            . mb_substr($fathers[$key]['forename'], 0, 1) . '. '
            . mb_substr($fathers[$key]['patronymic'], 0, 1) . '.',
        'name_full' => $fathers[$key]['surname'] . ' '
            . $fathers[$key]['forename'] . ' '
            . $fathers[$key]['patronymic'],
        'surname' => $fathers[$key]['surname'],
        'forename' => $fathers[$key]['forename'],
        'patronymic' => $fathers[$key]['patronymic'],
        'user_id' => $this->getFixtureModel(User::class, 'user-' . $key)->primaryKey,
    ];
    $user = User::findOne(['id' => $this->getFixtureModel(User::class, 'user-' . $key)->primaryKey]);
    $user->name_full = $fathers[$key]['surname'] . ' ' . $fathers[$key]['forename'] . ' ' . $fathers[$key]['patronymic'];
    $user->save();
}
return $result;