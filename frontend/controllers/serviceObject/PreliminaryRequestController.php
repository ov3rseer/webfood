<?php

namespace frontend\controllers\serviceObject;

/**
 * Контроллер для формы "Предварительная заявка"
 */
class PreliminaryRequestController extends RequestController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\request\PreliminaryRequest';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\serviceObject\request\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/request/preliminary-request/index',
            ],
        ]);
    }
}