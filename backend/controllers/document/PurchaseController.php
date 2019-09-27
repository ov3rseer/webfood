<?php

namespace backend\controllers\document;

use common\models\document\Request;

/**
 * Контроллер для документов "Покупки"
 */
class PurchaseController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Purchase';

    /**
     * @inheritdoc
     * @param Request $model
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        return $model->getTablePartColumns($tablePartRelation, $form, $readonly);
    }
}