<?php

namespace common\models\reference;

/**
 * Модель справочника "Категория продуктов"
 *
 * @property string   $plural_name
 */
class ProductCategory extends Reference
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['plural_name'], 'string'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'plural_name' => 'Наименование во множественном числе',
        ]);
    }
}