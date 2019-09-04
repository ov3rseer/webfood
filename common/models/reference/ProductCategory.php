<?php

namespace common\models\reference;

/**
 * Модель справочника "Категория продуктов"
 */
class ProductCategory extends Category
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Категория продуктов';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Категории продуктов';
    }
}