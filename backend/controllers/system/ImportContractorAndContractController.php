<?php

namespace backend\controllers\system;

/**
 * Контроллер для управления импортом контрагентов и договоров
 */
class ImportContractorAndContractController extends SystemController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'backend\models\form\import\ImportContractorAndContractForm';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\system\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/system/import-contractor-and-contract/index',
            ],
        ]);
    }
}