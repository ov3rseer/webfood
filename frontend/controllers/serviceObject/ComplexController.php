<?php

namespace frontend\controllers\serviceObject;

use backend\controllers\BackendModelController;
use common\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Контроллер для формы "Комплексы"
 */
class ComplexController extends BackendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Complex';

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
                        'actions' => ['index', 'delete-checked', 'delete', 'create', 'update'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-checked' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/base/index',
            ],
            'search' => [
                'class' => 'backend\actions\reference\base\SearchAction',
                'modelClass' => $this->modelClass,
                'searchFields' => ['name'],
            ],
        ]);
    }
}