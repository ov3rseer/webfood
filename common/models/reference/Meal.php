<?php

namespace common\models\reference;

use common\models\enum\MealType;
use common\models\tablepart\MealProduct;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Блюдо"
 *
 * Свойства
 * @property integer $meal_category_id
 * @property float   $price
 * @property string  $description
 *
 * Отношения:
 * @property MealProduct[] $mealProduct
 * @property MealCategory $mealCategory
 */
class Meal extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Блюдо';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Блюда';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['meal_category_id', 'meal_type_id'], 'integer'],
            [['meal_category_id', 'meal_type_id', 'price'], 'required'],
            [['description'], 'string'],
            [['price'], 'number', 'min' => 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'mealProducts'      => 'Продукты (состав блюда)',
            'meal_category_id'  => 'Категория блюда',
            'meal_type_id'      => 'Тип блюда',
            'price'             => 'Цена',
            'description'       => 'Описание',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMealCategory()
    {
        return $this->hasOne(MealCategory::className(), ['id' => 'meal_category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMealType()
    {
        return $this->hasOne(MealType::className(), ['id' => 'meal_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMealProducts()
    {
        return $this->hasMany(MealProduct::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'mealProducts' => MealProduct::className(),
        ], parent::getTableParts());
    }
}