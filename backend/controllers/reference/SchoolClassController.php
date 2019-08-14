<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Классы"
 */
class SchoolClassController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\SchoolClass';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => 'backend\actions\base\CreateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/school-class/update',
            ],
            'update' => [
                'class' => 'backend\actions\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/school-class/update',
            ],
        ]);
    }
}