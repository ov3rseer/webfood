<?php

namespace frontend\controllers\serviceObject;

use frontend\controllers\FrontendModelController;

/**
 * Контроллер для формы "Загрузка списков"
 */
class UploadListsController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\UploadLists';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/upload-lists/index',
            ],
        ]);
    }
}