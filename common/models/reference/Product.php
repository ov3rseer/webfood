<?php

namespace common\models\reference;

use yii\db\ActiveQuery;

/**
 * Модель справочника "Продукт"
 *
 * @property integer    $product_code
 * @property integer    $unit_id
 * @property integer    $product_category_id
 *
 * Отношения:
 * @property Unit               $unit
 * @property ProductCategory    $productCategory
 */
class Product extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Продукт';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Продукты';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_code', 'unit_id', 'product_category_id'], 'integer'],
            [['product_code'], 'required'],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_code'          => 'Код продукта',
            'unit_id'               => 'Единица измерения',
            'product_category_id'   => 'Категория продукта',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductCategory()
    {
        return $this->hasOne(ProductCategory::class, ['id' => 'product_category_id']);
    }
}
