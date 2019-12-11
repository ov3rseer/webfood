<?php

namespace backend\controllers\document;

/**
 * Контроллер для документов "Поступления продуктов"
 */
class ArrivalController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Arrival';
}