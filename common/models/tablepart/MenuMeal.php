<?php

namespace common\models\tablepart;

use common\models\reference\Meal;
use common\models\reference\Menu;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Блюда" справочника "Меню"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $meal_id
 *
 * Отношения:
 * @property Menu $parent
 * @property Meal $meal
 */
class MenuMeal extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['meal_id'], 'integer'],
            [['meal_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'meal_id' => 'Блюдо',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMeal()
    {
        return $this->hasOne(Meal::class, ['id' => 'meal_id']);
    }
}