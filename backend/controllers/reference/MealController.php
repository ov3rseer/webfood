<?php

namespace backend\controllers\reference;

use common\models\reference\Meal;

/**
 * Контроллер для справочника "Блюда"
 */
class MealController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Meal';

    /**
     * @inheritdoc
     * @param Meal $model
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        return $model->getTablePartColumns($tablePartRelation, $form, $readonly);
    }
}