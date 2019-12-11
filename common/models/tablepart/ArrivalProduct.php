<?php

namespace common\models\tablepart;

use common\models\document\Arrival;
use common\models\reference\Product;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Продукты" документа "Поступление продуктов"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $product_id
 * @property float $quantity
 *
 * Отношения:
 * @property Arrival $parent
 * @property Product $product
 */
class ArrivalProduct extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id'], 'integer'],
            [['product_id', 'quantity'], 'required'],
            [['quantity'], 'number', 'min' => 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_id' => 'Продукт',
            'quantity' => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Arrival::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->andWhere(['product_provider_id' => $this->parent->product_provider_id]);
    }
}