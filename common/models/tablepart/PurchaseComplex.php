<?php

namespace common\models\tablepart;

use common\models\document\Purchase;
use common\models\reference\Complex;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Купленные комплексы" документа "Покупка"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $complex_id
 * @property float $quantity
 * @property float $price
 *
 * Отношения:
 * @property Purchase $parent
 * @property Complex $complex
 */
class PurchaseComplex extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['complex_id'], 'integer'],
            [['quantity', 'price'], 'number', 'min' => 0],
            [['complex_id', 'quantity', 'price'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'complex_id' => 'Комплекс',
            'quantity' => 'Количество',
            'price' => 'Цена',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Purchase::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplex()
    {
        return $this->hasOne(Complex::className(), ['id' => 'complex_id']);
    }
}