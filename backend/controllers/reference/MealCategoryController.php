<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Категории блюд"
 */
class MealCategoryController extends CategoryController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\MealCategory';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/meal-category/index',
            ],
        ]);
    }
}