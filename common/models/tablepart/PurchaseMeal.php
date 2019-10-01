<?php

namespace common\models\tablepart;

use common\models\document\Purchase;
use common\models\reference\Meal;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Купленные блюда" документа "Покупка"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $meal_id
 * @property float   $quantity
 *
 * Отношения:
 * @property Purchase $parent
 * @property Meal $meal
 */
class PurchaseMeal extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['meal_id'], 'integer'],
            [['quantity'], 'number', 'min' => 0],
            [['meal_id', 'quantity'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'meal_id' => 'Блюдо',
            'quantity' => 'Количество',
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
    public function getMeal()
    {
        return $this->hasOne(Meal::className(), ['id' => 'meal_id']);
    }
}