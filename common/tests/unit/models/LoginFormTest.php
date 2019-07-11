<?php

namespace common\tests\unit\models;

use Codeception\Test\Unit;
use Yii;
use common\models\LoginForm;
use common\fixtures\UserFixture;

/**
 * Login form test
 */
class LoginFormTest extends Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;


    /**
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'ref_user.php'
            ]
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'login' => 'not_existing_username',
            'password' => 'not_existing_password',
        ]);

        expect('model should not login user', $model->login())->false();
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'login' => 'bayer.hudson',
            'password' => 'wrong_password',
        ]);

        expect('model should not login user', $model->login())->false();
        expect('error message should be set', $model->errors)->hasKey('password');
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'login' => 'bayer.hudson',
            'password' => 'password_0',
        ]);

        expect('model should login user', $model->login())->true();
        expect('error message should not be set', $model->errors)->hasntKey('password');
        expect('user should be logged in', Yii::$app->user->isGuest)->false();
    }
}
