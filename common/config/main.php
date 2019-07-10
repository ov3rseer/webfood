<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'ru-RU',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'authManager' => [
            'class'           => 'common\components\DbManager',
            'itemTable'       => '{{%sys_auth_item}}',
            'itemChildTable'  => '{{%sys_auth_item_child}}',
            'assignmentTable' => '{{%sys_auth_assignment}}',
            'ruleTable'       => '{{%sys_auth_rule}}',
            'cache'           => 'cache',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
    ],
];
