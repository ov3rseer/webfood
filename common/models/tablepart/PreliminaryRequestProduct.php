<?php


namespace common\models\tablepart;

use common\models\reference\Product;
use common\models\reference\Unit;

class PreliminaryRequestProduct extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id', 'unit_id', 'quantity'], 'required'],
            [['product_id', 'unit_id'], 'integer'],
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
            'unit_id'       => 'Единица измерения',
            'quantity'      => 'Количество',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }
}