<?php

namespace common\models\reference;

use common\models\enum\MenuCycle;
use common\models\enum\WeekDay;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Установка меню"
 *
 * Свойства:
 * @property integer $menu_id
 * @property integer $menu_cycle_id
 * @property integer $week_day_id
 *
 * Отношения:
 * @property Menu $menu
 * @property MenuCycle $menuCycle
 * @property WeekDay $weekDay
 */
class SetMenu extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Установка меню';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Установки меню';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['menu_id', 'menu_cycle_id', 'week_day_id'], 'integer'],
            [['menu_id', 'menu_cycle_id', 'week_day_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'menu_id'       => 'Меню',
            'menu_cycle_id' => 'Цикличность меню',
            'week_day_id'   => 'День недели',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::class, ['id' => 'menu_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMenuCycle()
    {
        return $this->hasOne(MenuCycle::class, ['id' => 'menu_cycle_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeekDay()
    {
        return $this->hasOne(WeekDay::class, ['id' => 'week_day_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            $this->name = $this->menu->name . ' (' . $this->weekDay . ' - ' . $this->menuCycle . ')';
        }
        return $parentResult;
    }
}