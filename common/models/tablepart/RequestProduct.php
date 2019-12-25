<?php

namespace common\models\tablepart;

use common\models\document\Request;
use common\models\reference\Product;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Продукты" документа "Заявка"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $product_id
 * @property float $quantity
 *
 * Отношения:
 * @property Request $parent
 * @property Product $product
 */
class RequestProduct extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id'], 'integer'],
            [['quantity'], 'number', 'min' => 0],
            [['product_id', 'quantity'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_id'    => 'Продукт',
            'quantity'      => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Request::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id'])->andWhere(['product_provider_id' => $this->parent->product_provider_id]);
    }
}