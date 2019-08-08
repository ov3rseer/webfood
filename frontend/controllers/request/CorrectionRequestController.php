<?php

namespace frontend\controllers\request;

/**
 * Контроллер для формы "Корректировка заявки"
 */
class CorrectionRequestController extends RequestController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\request\CorrectionRequestForm';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\request\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/request/correction-request/index',
            ],
        ]);
    }
}