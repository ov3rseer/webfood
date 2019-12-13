<?php

namespace backend\controllers\document;

use common\models\document\Arrival;

/**
 * Контроллер для документов "Поступления продуктов"
 */
class ArrivalController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Arrival';

    /**
     * @inheritdoc
     * @param Arrival $model
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        return $model->getTablePartColumns($tablePartRelation, $form, $readonly);
    }
}