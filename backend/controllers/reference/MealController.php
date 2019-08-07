<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Блюда"
 */
class MealController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Meal';
}