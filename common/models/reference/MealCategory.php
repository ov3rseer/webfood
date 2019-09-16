<?php

namespace common\models\reference;

/**
 * Модель справочника "Категория блюда"
 */
class MealCategory extends Category
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Категория блюда';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Категории блюд';
    }
}