<?php

namespace frontend\tests\unit\models;

use Yii;
use frontend\models\PasswordResetRequestForm;
use common\fixtures\UserFixture;
use common\models\reference\User;
use yii\base\Exception;

class PasswordResetRequestFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'ref_user.php'
            ]
        ]);
    }

    /**
     * @throws Exception
     */
    public function testSendMessageWithWrongEmailAddress()
    {
        $model = new PasswordResetRequestForm();
        $model->email = 'not-existing-email@example.com';
        expect_not($model->sendEmail());
    }

    /**
     * @throws Exception
     */
    public function testNotSendEmailsToInactiveUser()
    {
        $user = $this->tester->grabFixture('user', 6);
        $model = new PasswordResetRequestForm();
        $model->email = $user['email'];
        expect_not($model->sendEmail());
    }

    /**
     * @throws Exception
     */
    public function testSendEmailSuccessfully()
    {
        $userFixture = $this->tester->grabFixture('user', 0);
        
        $model = new PasswordResetRequestForm();
        $model->email = $userFixture['email'];
        $user = User::findOne(['password_reset_token' => $userFixture['password_reset_token']]);

        expect_that($model->sendEmail());
        expect_that($user->password_reset_token);

        $emailMessage = $this->tester->grabLastSentEmail();
        expect('valid email is sent', $emailMessage)->isInstanceOf('yii\mail\MessageInterface');
        expect($emailMessage->getTo())->hasKey($model->email);
        expect($emailMessage->getFrom())->hasKey(Yii::$app->params['supportEmail']);
    }
}
