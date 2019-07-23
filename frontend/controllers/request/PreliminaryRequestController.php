<?php

namespace frontend\controllers\request;

use frontend\controllers\FrontendModelController;

/**
 * Контроллер для формы "Предварительная заявка"
 */
class PreliminaryRequestController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\request\PreliminaryRequest';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/request/preliminary-request/index',
            ],
        ]);
    }
}