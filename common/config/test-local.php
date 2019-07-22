<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=127.0.0.1;dbname=webfood',
            'username' => 'postgres',
            'password' => '1234',
            'charset' => 'utf8',
        ],
    ],
];
