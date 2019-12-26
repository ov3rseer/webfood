<?php

namespace frontend\controllers\productProvider;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;

/**
 * Контроллер для формы "Продукты"
 */
class ProductController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Product';

    /**
     * @var string имя класса формы
     */
    public $modelClassForm = 'frontend\models\productProvider\ProductForm';

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
                        'roles' => ['product-provider'],
                    ],
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
                'viewPath' => '@frontend/views/productProvider/product/index',
            ],
            'update' => [
                'class' => 'frontend\actions\product\UpdateAction',
                'modelClassForm' => $this->modelClassForm,
            ],
        ]);
    }
}