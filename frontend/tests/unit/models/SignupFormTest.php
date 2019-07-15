<?php
namespace frontend\tests\unit\models;

use Codeception\Test\Unit;
use common\fixtures\UserFixture;
use frontend\models\SignupForm;
use yii\base\Exception;

class SignupFormTest extends Unit
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
    public function testCorrectSignup()
    {
        $model = new SignupForm([
            'name' => 'some_username',
            'surname' => 'some_username',
            'forename' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
            'password_repeat' => 'some_password',
        ]);

        $user = $model->signup();
        expect($user)->true();

        /** @var \common\models\reference\User $user */
        $user = $this->tester->grabRecord('common\models\reference\User', [
            'name' => 'some_username',
            'email' => 'some_email@example.com',
        ]);

        $this->tester->seeEmailIsSent();

        $mail = $this->tester->grabLastSentEmail();

        expect($mail)->isInstanceOf('yii\mail\MessageInterface');
        expect($mail->getTo())->hasKey('some_email@example.com');
        expect($mail->getFrom())->hasKey(\Yii::$app->params['supportEmail']);
        expect($mail->getSubject())->equals('Account registration at ' . \Yii::$app->name);
        expect($mail->toString())->contains($user->verification_token);
    }

    /**
     * @throws Exception
     */
    public function testNotCorrectSignup()
    {
        $model = new SignupForm([
            'name' => 'troy.becker',
            'email' => 'nicolas.dianna@hotmail.com',
            'password' => 'some_password',
        ]);

        expect_not($model->signup());
        expect_that($model->getErrors('name'));
        expect_that($model->getErrors('email'));

        expect($model->getFirstError('name'))
            ->equals('This username has already been taken.');
        expect($model->getFirstError('email'))
            ->equals('This email address has already been taken.');
    }
}
