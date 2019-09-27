<?php

namespace backend\controllers\reference;

use common\models\document\Request;

/**
 * Контроллер для справочника "Комплексы"
 */
class ComplexController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Complex';

    /**
     * @inheritdoc
     * @param Request $model
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        return $model->getTablePartColumns($tablePartRelation, $form, $readonly);
    }
}