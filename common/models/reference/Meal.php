<?php


namespace common\models\reference;

use common\models\tablepart\MealProduct;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Блюдо"
 *
 * Отношения:
 * @property MealProduct[] $mealProduct
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
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'mealProducts' => 'Продукты (состав блюда)',
        ]);
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