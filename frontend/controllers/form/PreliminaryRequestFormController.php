<?php

namespace frontend\controllers\form;

use frontend\controllers\FrontendModelController;


class PreliminaryRequestFormController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\form\PreliminaryRequestForm';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/form/preliminary-request/index',
            ],
        ]);
    }
}