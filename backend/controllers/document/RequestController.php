<?php

namespace backend\controllers\document;

use common\models\document\Request;

/**
 * Контроллер для документов "Заявки"
 */
class RequestController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Request';

    /**
     * @inheritdoc
     * @param Request $model
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        return $model->getTablePartColumns($tablePartRelation, $form, $readonly);
    }
}