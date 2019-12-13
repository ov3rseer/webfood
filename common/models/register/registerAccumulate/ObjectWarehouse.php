<?php

namespace common\models\register\registerAccumulate;

use common\models\reference\Product;
use common\models\reference\ServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель "Склады объектов обслуживания"
 *
 * Свойства:
 * @property integer $service_object
 * @property integer $product_id
 * @property float   $quantity
 *
 * Отношения:
 * @property ServiceObject $serviceObject
 * @property Product $product
 */
class ObjectWarehouse extends RegisterAccumulate
{
    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Склады объектов обслуживания';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_id', 'product_id', 'quantity'], 'required'],
            [['service_object_id', 'product_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object_id'     => 'Объект обслуживания',
            'product_id'            => 'Продукт',
            'quantity'              => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObject()
    {
        return $this->hasOne(ServiceObject::class, ['id' => 'service_object_id']);
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
            'service_object_id',
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