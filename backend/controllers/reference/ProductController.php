<?php


namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Продукты"
 */
class ProductController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Product';
}