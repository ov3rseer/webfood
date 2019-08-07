<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Объекты обслживания"
 */
class ServiceObjectController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\ServiceObject';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update' => [
                'class' => 'backend\actions\reference\service_object\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/update',
            ],
        ]);
    }
}