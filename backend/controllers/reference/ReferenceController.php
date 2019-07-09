<?php

namespace backend\controllers\reference;

use backend\controllers\ModelController;

/**
 * Базовый класс контроллера для моделей справочников
 */
abstract class ReferenceController extends ModelController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'search' => [
                'class' => 'backend\actions\base\SearchAction',
                'modelClass' => $this->modelClass,
                'searchFields' => ['name'],
            ],
        ]);
    }
}
