<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [
            'class' => 'common\components\pgsql\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=webfood_extend',
            'username' => 'postgres',
            'password' => '1234',
            'enableSchemaCache' => true,
            'schemaCache' => 'schemaCache',
        ],
        'dbAdmin' => [
            'class' => 'common\components\pgsql\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=postgres',
            'username' => 'postgres',
            'password' => '1234',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'console\fixtures',
            'globalFixtures' => [],
        ],
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'templateFile' => '@console/migrations/template.php.txt',
            'migrationTable' => '{{%sys_migration}}',
        ],
        'task' => [
            'class' => 'console\controllers\TaskController',
        ],
        'database' => [
            'class' => 'console\controllers\DatabaseController',
            'connection' => 'dbAdmin',
            'name' => 'webfood_extend',
            'owner' => 'postgres',
        ],
    ],
    'params' => $params,
];
