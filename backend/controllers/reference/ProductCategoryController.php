<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Категории продуктов"
 */
class ProductCategoryController extends CategoryController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\ProductCategory';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/product-category/index',
            ],
        ]);
    }
}