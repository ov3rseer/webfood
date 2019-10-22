<?php

namespace frontend\controllers\serviceObject;

use common\models\reference\ProductCategory;

/**
 * Контроллер для формы "Категории продуктов"
 */
class ProductCategoryController extends CategoryController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\ProductCategoryForm';

    /**
     * @var string имя модели категорий
     */
    public $categoryModel = ProductCategory::class;
}