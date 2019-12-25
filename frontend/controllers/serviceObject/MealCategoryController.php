<?php

namespace frontend\controllers\serviceObject;

use common\models\reference\MealCategory;

/**
 * Контроллер для формы "Категории блюд"
 */
class MealCategoryController extends CategoryController
{
    /**
     * @var string имя класса модели
     */
    public $modelClassForm = 'frontend\models\serviceObject\MealCategoryForm';

    /**
     * @var string имя модели категорий
     */
    public $categoryModel = MealCategory::class;
}