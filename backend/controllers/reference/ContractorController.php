<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Контрагенты"
 */
class ContractorController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Contractor';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'update' => [
                'class' => 'backend\actions\reference\contractor\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/update',
            ],
        ]);
    }
}