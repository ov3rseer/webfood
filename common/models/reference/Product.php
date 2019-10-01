<?php

namespace common\models\reference;

use yii\db\ActiveQuery;

/**
 * Модель справочника "Продукт"
 *
 * @property integer    $product_code
 * @property integer    $unit_id
 * @property integer    $product_category_id
 * @property float      $price
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
            [['price'], 'number', 'min' => 0],
            [['name', 'product_code', 'price', 'unit_id', 'product_category_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_code'          => 'Код продукта',
            'price'                 => 'Цена за единицу измерения',
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
