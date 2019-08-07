<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Поставщики продуктов"
 */
class ProductProviderController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\ProductProvider';
}