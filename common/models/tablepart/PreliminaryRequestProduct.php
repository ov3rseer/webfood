<?php


namespace common\models\tablepart;


use common\models\document\PreliminaryRequest;
use common\models\reference\Product;
use common\models\reference\Unit;

/**
 * Модель строки табличной части "Производитель" документа "Сертификат соответствия"
 *
 * Свойства:
 * @property integer $product_id
 * @property integer $unit_id
 * @property float   $quantity
 *
 * Отношения:
 * @property PreliminaryRequest    $parent
 * @property Product               $nomenclature
 * @property Unit                  $unit
 */
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }
}