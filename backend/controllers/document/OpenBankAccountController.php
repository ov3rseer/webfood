<?php

namespace backend\controllers\document;

/**
 * Контроллер для документов "Открытие счетов"
 */
class OpenBankAccountController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\OpenBankAccount';
}