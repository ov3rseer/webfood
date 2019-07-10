<?php

namespace backend\controllers\system;

use backend\controllers\ModelController;
use common\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * Базовый класс контроллера для системных моделей
 */
abstract class SystemController extends ModelController
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
     * @throws \yii\web\BadRequestHttpException
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
                        'roles'   => [static::className() . '.Index'],
                    ],
                ],
            ],
        ]);
    }
}
