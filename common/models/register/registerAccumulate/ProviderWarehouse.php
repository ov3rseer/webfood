<?php

namespace common\models\register\registerAccumulate;

use common\models\reference\Product;
use common\models\reference\ProductProvider;
use yii\db\ActiveQuery;

/**
 * Модель "Склады постащиков"
 *
 * Свойства:
 * @property integer $product_provider_id
 * @property integer $product_id
 * @property float   $quantity
 *
 * Отношения:
 * @property ProductProvider $productProvider
 * @property Product $product
 */
class ProviderWarehouse extends RegisterAccumulate
{
    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Склады постащиков';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_provider_id', 'product_id', 'quantity'], 'required'],
            [['product_provider_id', 'product_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_provider_id'   => 'Поставщик продуктов',
            'product_id'            => 'Продукт',
            'quantity'              => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductProvider()
    {
        return $this->hasOne(ProductProvider::class, ['id' => 'product_provider_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @inheritdoc
     */
    static public function getDimensions()
    {
        return [
            'product_provider_id',
            'product_id',
        ];
    }

    /**
     * @inheritdoc
     */
    static public function getResources()
    {
        return [
            'quantity',
        ];
    }
}