<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Базовый контроллер для форм "Категорий"
 */
abstract class CategoryController extends FrontendModelController
{
    /**
     * @var string имя модели категорий
     */
    public $categoryModel = null;

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
                        'actions' => ['index', 'delete-checked', 'delete', 'update'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update' => ['POST'],
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
                'class' => 'frontend\actions\base\IndexAction',
                'modelClassForm' => $this->modelClassForm,
                'viewPath' => '@frontend/views/service-object/category/index',
            ],
        ]);
    }
}