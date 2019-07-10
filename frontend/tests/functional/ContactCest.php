<?php
namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

/* @var $scenario \Codeception\Scenario */

class ContactCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage(['site/contact']);
    }

    public function checkContact(FunctionalTester $I)
    {
        $I->see('Contact', 'h1');
    }

    public function checkContactSubmitNoData(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', []);
        $I->see('Contact', 'h1');
        $I->seeValidationError('Необходимо заполнить «Name».');
        $I->seeValidationError('Необходимо заполнить «Email»');
        $I->seeValidationError('Необходимо заполнить «Subject».');
        $I->seeValidationError('Необходимо заполнить «Body».');
        $I->seeValidationError('Неправильный проверочный код.');
    }

    public function checkContactSubmitNotCorrectEmail(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester.email',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
            'ContactForm[verifyCode]' => 'testme',
        ]);
        $I->seeValidationError('Значение «Email» не является правильным email адресом.');
        $I->dontSeeValidationError('Необходимо заполнить «Name».');
        $I->dontSeeValidationError('Необходимо заполнить «Subject».');
        $I->dontSeeValidationError('Необходимо заполнить «Body».');
        $I->dontSeeValidationError('Неправильный проверочный код.');
    }

    public function checkContactSubmitCorrectData(FunctionalTester $I)
    {
        $I->submitForm('#contact-form', [
            'ContactForm[name]' => 'tester',
            'ContactForm[email]' => 'tester@example.com',
            'ContactForm[subject]' => 'test subject',
            'ContactForm[body]' => 'test content',
            'ContactForm[verifyCode]' => 'testme',
        ]);
        $I->seeEmailIsSent();
        $I->see('Благодарим вас за обращение. Мы ответим вам как можно скорее.');
    }
}
