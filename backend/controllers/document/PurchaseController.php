<?php

namespace backend\controllers\document;

/**
 * Контроллер для документов "Покупки"
 */
class PurchaseController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Purchase';
}