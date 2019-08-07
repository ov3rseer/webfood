<?php

namespace common\models\tablepart;

use common\models\reference\Complex;
use common\models\reference\Meal;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Блюда (состав комплекса)" справочника "Комплекс"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $meal_id
 * @property float   $meal_quantity
 *
 * Отношения:
 * @property Complex    $parent
 * @property Meal       $meal
 */
class ComplexMeal extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['meal_id'], 'integer'],
            [['meal_id', 'meal_quantity'], 'required'],
            [['meal_quantity'], 'number', 'min' => 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'meal_id'        => 'Блюдо',
            'meal_quantity'  => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Complex::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMeal()
    {
        return $this->hasOne(Meal::className(), ['id' => 'meal_id']);
    }
}