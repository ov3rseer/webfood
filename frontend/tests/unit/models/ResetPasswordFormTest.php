<?php

namespace frontend\tests\unit\models;

use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use frontend\models\ResetPasswordForm;
use frontend\tests\UnitTester;

class ResetPasswordFormTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'ref_user.php'
            ],
        ]);
    }

    public function testResetWrongToken()
    {
        $this->tester->expectException('\yii\base\InvalidArgumentException', function() {
            new ResetPasswordForm('');
        });

        $this->tester->expectException('\yii\base\InvalidArgumentException', function() {
            new ResetPasswordForm('notexistingtoken_1391882543');
        });
    }

    /**
     * @throws \yii\base\Exception
     */
    public function testResetCorrectToken()
    {
        $user = $this->tester->grabFixture('user', 0);
        $form = new ResetPasswordForm($user['password_reset_token']);
        expect_that($form->resetPassword());
    }

}
