<?php

namespace backend\controllers\document;

/**
 * Контроллер для документов "Пополнение счетов"
 */
class RefillBalanceController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\RefillBalance';
}