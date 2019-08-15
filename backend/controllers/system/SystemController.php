<?php

namespace backend\controllers\system;

use backend\controllers\BackendModelController;
use common\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;

/**
 * Базовый класс контроллера для системных моделей
 */
abstract class SystemController extends BackendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\system\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/system/base/index',
            ],
        ]);
    }

    /**
     * @inheritdoc
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        ini_set('max_execution_time', 180);
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => ['super-admin'],
                    ],
                ],
            ],
        ]);
    }
}
