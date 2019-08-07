<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Категории продуктов"
 */
class ProductCategoryController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\ProductCategory';
}