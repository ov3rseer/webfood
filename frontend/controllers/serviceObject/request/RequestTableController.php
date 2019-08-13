<?php

namespace frontend\controllers\serviceObject\request;

use frontend\controllers\FrontendModelController;

class RequestTableController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\request\RequestTableForm';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\requesttable\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/request/request-table/index',
            ],
        ]);
    }
}