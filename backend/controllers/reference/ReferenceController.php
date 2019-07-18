<?php

namespace backend\controllers\reference;

use backend\controllers\BackendModelController;

/**
 * Базовый класс контроллера для моделей справочников
 */
abstract class ReferenceController extends BackendModelController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/base/index',
            ],
            'search' => [
                'class' => 'backend\actions\reference\base\SearchAction',
                'modelClass' => $this->modelClass,
                'searchFields' => ['name'],
            ],
        ]);
    }
}
