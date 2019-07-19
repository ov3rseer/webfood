<?php
return [
    'components' => [
        'db' => [
            'class' => 'common\components\pgsql\Connection',
            'schemaMap' => ['pgsql' => 'common\components\pgsql\Schema'],
            'charset' => 'utf8',
            'dsn' => 'pgsql:host=127.0.0.1;dbname=webfood',
            'username' => 'postgres',
            'password' => '1234',
            'enableSchemaCache' => true,
            'schemaCache' => 'schemaCache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'webfood.test@gmail.com',
                'password' => 'GrDM7b6h57HvZCq',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
    ],
];
