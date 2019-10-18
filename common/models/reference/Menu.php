<?php

namespace common\models\reference;

use common\models\tablepart\MenuComplex;
use common\models\tablepart\MenuMeal;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Меню"
 *
 * Отношения:
 * @property MenuComplex[]  $menuComplexes
 * @property MenuMeal[]     $menuMeals
 */
class Menu extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Меню';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Меню';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'menuComplexes' => 'Комплексы',
            'menuMeals' => 'Блюда',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMenuComplexes()
    {
        return $this->hasMany(MenuComplex::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getMenuMeals()
    {
        return $this->hasMany(MenuMeal::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'menuComplexes' => MenuComplex::class,
            'menuMeals' => MenuMeal::class,
        ], parent::getTableParts());
    }
}