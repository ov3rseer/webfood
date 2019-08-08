<?php

namespace frontend\controllers\request;

use frontend\controllers\FrontendModelController;

class RequestTableController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\request\RequestTableForm';

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