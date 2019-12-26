<?php

namespace common\models\reference;

use common\models\register\registerAccumulate\ProviderWarehouse;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Продукт"
 *
 * @property integer    $unit_id
 * @property integer    $product_category_id
 * @property integer    $product_provider_id
 * @property float      $price
 *
 * Отношения:
 * @property Unit                   $unit
 * @property ProductCategory        $productCategory
 * @property ProductProvider        $productProvider
 * @property ProviderWarehouse[]    $providerWarehouse
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
            [['unit_id', 'product_category_id', 'product_provider_id'], 'integer'],
            [['price'], 'number', 'min' => 0],
            [['price', 'unit_id', 'product_category_id', 'product_provider_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'price'                 => 'Цена за единицу измерения',
            'unit_id'               => 'Единица измерения',
            'product_category_id'   => 'Категория продукта',
            'product_provider_id'   => 'Поставщик',
            'quantity'              => 'Количество на складе',
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
    public function getProviderWarehouse()
    {
        return $this->hasMany(ProviderWarehouse::class, ['product_id' => 'id']);
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        $fields = [
            'quantity' => function ($model) {
                return $model->getQuantity();
            },
        ];
        return array_merge(parent::fields(), $fields);
    }

    /**
     * @return false|string|null
     * @throws InvalidConfigException
     */
    public function getQuantity()
    {
        return ProviderWarehouse::find()
            ->select('SUM(quantity) AS quantity')
            ->where(['product_id' => $this->id])
            ->groupBy(['product_id'])
            ->scalar();
    }
}
