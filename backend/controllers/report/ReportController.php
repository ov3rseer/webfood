<?php

namespace backend\controllers\report;

use backend\controllers\BackendModelController;
use common\helpers\ArrayHelper;
use yii\filters\AccessControl;

/**
 * Базовый контроллер для отчетов
 */
class ReportController extends BackendModelController
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
                'class' => 'backend\actions\report\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/report/base/index',
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
