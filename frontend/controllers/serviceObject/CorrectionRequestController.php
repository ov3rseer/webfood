<?php

namespace frontend\controllers\serviceObject;

/**
 * Контроллер для формы "Корректировка заявки"
 */
class CorrectionRequestController extends RequestController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\request\CorrectionRequest';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\serviceObject\request\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/request/correction-request/index',
            ],
        ]);
    }
}