<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

class SignupCest
{
    protected $formId = '#form-signup';


    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/signup');
    }

    public function signupWithEmptyFields(FunctionalTester $I)
    {
        $I->see('Регистрация', 'h1');
        $I->see('Пожалуйста, заполните следующие поля для регистрации:');
        $I->submitForm($this->formId, []);
        $I->seeValidationError('Необходимо заполнить «Логин».');
        $I->seeValidationError('Необходимо заполнить «Фамилия».');
        $I->seeValidationError('Необходимо заполнить «Имя».');
        $I->seeValidationError('Необходимо заполнить «Email».');
        $I->seeValidationError('Необходимо заполнить «Пароль».');
        $I->seeValidationError('Необходимо заполнить «Повторите пароль».');

    }

    public function signupWithWrongEmail(FunctionalTester $I)
    {
        $I->submitForm(
            $this->formId, [
                'SignupForm[username]' => 'tester',
                'SignupForm[surname]' => 'tester',
                'SignupForm[name]' => 'tester',
                'SignupForm[email]' => 'ttttt',
                'SignupForm[password]' => 'tester_password',
                'SignupForm[password_repeat]' => 'tester_password',
            ]
        );
        $I->dontSee('Необходимо заполнить «Логин».', '.help-block');
        $I->dontSee('Необходимо заполнить «Фамилия».', '.help-block');
        $I->dontSee('Необходимо заполнить «Имя».', '.help-block');
        $I->dontSee('Необходимо заполнить «Email».', '.help-block');
        $I->dontSee('Необходимо заполнить «Пароль».', '.help-block');
        $I->dontSee('Необходимо заполнить «Повторите пароль».', '.help-block');
        $I->see('Значение «Email» не является правильным email адресом.', '.help-block');
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[username]' => 'tester',
            'SignupForm[surname]' => 'tester',
            'SignupForm[name]' => 'tester',
            'SignupForm[email]' => 'tester.email@example.com',
            'SignupForm[password]' => 'tester_password',
            'SignupForm[password_repeat]' => 'tester_password',
        ]);

        $I->seeRecord('common\models\reference\User', [
            'username' => 'tester',
            'email' => 'tester.email@example.com',
        ]);

        $I->seeEmailIsSent();
        $I->see('Спасибо за регистрацию. Пожалуйста, проверьте свой email для подтверждения регистрации.');
    }
}
