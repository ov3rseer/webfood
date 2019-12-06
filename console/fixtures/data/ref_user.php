<?php

use common\models\enum\UserType;
use console\fixtures\Fixture;

/** @var Fixture $this */
/** @noinspection PhpUnhandledExceptionInspection */

$users = [
    'super-admin' => [
        'id' => 1,
        'is_active' => true,
        'name' => 'admin',
        'name_full' => 'Администратор',
        'user_type_id' => UserType::ADMIN,
        'email' => 'admin@webfood.test',
        'password_hash' => Yii::$app->security->generatePasswordHash('admin')
    ],
    'user-father-1' => [
        'id' => 2,
        'is_active' => true,
        'name' => 'father1',
        'user_type_id' => UserType::FATHER,
        'email' => 'father1@webfood.test',
        'password_hash' => Yii::$app->security->generatePasswordHash('father1')
    ],
    'user-father-2' => [
        'id' => 3,
        'is_active' => true,
        'name' => 'father2',
        'user_type_id' => UserType::FATHER,
        'email' => 'father2@webfood.test',
        'password_hash' => Yii::$app->security->generatePasswordHash('father2')
    ],
    'user-object-1' => [
        'id' => 4,
        'is_active' => true,
        'name' => 'object1',
        'user_type_id' => UserType::SERVICE_OBJECT,
        'email' => 'object1@webfood.test',
        'password_hash' => Yii::$app->security->generatePasswordHash('object1')
    ],
    'user-object-2' => [
        'id' => 5,
        'is_active' => true,
        'name' => 'object2',
        'user_type_id' => UserType::SERVICE_OBJECT,
        'email' => 'object2@webfood.test',
        'password_hash' => Yii::$app->security->generatePasswordHash('object2')
    ],
    'user-provider-1' => [
        'id' => 6,
        'is_active' => true,
        'name' => 'provider1',
        'user_type_id' => UserType::PRODUCT_PROVIDER,
        'email' => 'provider1@webfood.test',
        'password_hash' => Yii::$app->security->generatePasswordHash('provider1')
    ],
];

$auth = Yii::$app->authManager;
foreach ($users as $userKey => $user) {
    if ($userKey != 'super-admin') {
        $role = \common\models\reference\User::checkRole($user['user_type_id']);
        if ($role) {
            $role = $auth->getRole($role);
            $auth->revoke($role, $user['id']);
        }
    }
}
foreach ($users as $userKey => $user) {
    if ($userKey != 'super-admin') {
        $role = \common\models\reference\User::checkRole($user['user_type_id']);
        if ($role) {
            $role = $auth->getRole($role);
            $auth->assign($role, $user['id']);
        }
    }
}
return $users;