<?php

namespace common\models\tablepart;

use common\models\reference\Meal;
use common\models\reference\Product;
use common\models\reference\Unit;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Продукты(состав блюда)" справочника "Блюдо"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $product_id
 * @property integer $unit_id
 * @property float   $product_quantity
 *
 * Отношения:
 * @property Meal       $parent
 * @property Product    $product
 * @property Unit       $unit
 */
class MealProduct extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id', 'unit_id'], 'integer'],
            [['product_id', 'unit_id', 'product_quantity'], 'required'],
            [['product_quantity'], 'number', 'min' => 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_id'        => 'Продукт',
            'unit_id'           => 'Единица измерения',
            'product_quantity'  => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Meal::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }
}