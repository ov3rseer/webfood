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
            [['unit_id'], 'validateUnit'],
        ]);
    }

    /**
     * Проверка на правильность единицы измерения
     * @throws \yii\base\InvalidConfigException
     */
    public function validateUnit()
    {
        if ($this->unit_id != $this->product->unit_id) {
            $units = Unit::find()->andWhere(['like', 'name_full', 'грамм'])->column();
            if (!in_array($this->unit_id, $units) || !in_array($this->product->unit_id, $units)) {
                $this->addError('unit_id', 'Выбрана неверная единица измерения.');
            }
        }
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
        return $this->hasOne(Meal::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }
}