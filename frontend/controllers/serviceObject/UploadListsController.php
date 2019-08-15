<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;

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
                'class' => 'frontend\actions\form\serviceObject\uploadLists\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/upload-lists/index',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ]
        ]);
    }
}