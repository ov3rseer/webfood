<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=webfood',
            'username' => 'root',
            'password' => '1234',
            'charset' => 'utf8',
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
