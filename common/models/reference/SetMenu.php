<?php

namespace common\models\document;

use common\components\DateTime;
use common\models\enum\MenuCycle;
use common\models\enum\WeekDay;
use common\models\reference\Menu;
use common\models\reference\Reference;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Установка меню"
 *
 * Свойства:
 * @property integer $menu_id
 * @property integer $menu_cycle_id
 * @property integer $week_day_id
 * @property DateTime $begin_date
 * @property DateTime $end_date
 * @property float $day
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
            [['begin_date', 'end_date'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
            [['menu_id', 'menu_cycle_id', 'week_day_id', 'begin_date', 'end_date'], 'required'],
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
            'begin_date'    => 'Начало периода питания',
            'end_date'      => 'Окончание периода питания',
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
}