<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use Yii;

class HomeCest
{
    public function checkOpen(FunctionalTester $I)
    {
        $I->amOnPage(Yii::$app->homeUrl);
        $I->see('WebFood');
        $I->seeLink('Регистрация');
        $I->click('Регистрация');
        $I->see('Регистрация', 'h1');
    }
}