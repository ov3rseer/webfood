<?php

namespace common\models\tablepart;

use common\models\document\PreliminaryRequest;
use common\models\reference\Product;

/**
 * Модель строки табличной части "Продукты" документа "Предварительная заявка"
 *
 * Свойства:
 * @property integer $product_id
 * @property float   $quantity
 *
 * Отношения:
 * @property PreliminaryRequest    $parent
 * @property Product               $product
 */
class PreliminaryRequestProduct extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id'], 'required'],
            [['product_id'], 'integer'],
            [['quantity'], 'number', 'min' => 0],
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
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(PreliminaryRequest::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}