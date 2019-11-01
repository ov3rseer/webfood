<?php

namespace frontend\models\serviceObject;

use common\models\enum\MenuCycle;
use common\models\enum\WeekDay;
use common\models\form\Form;
use common\models\reference\Menu;
use yii\base\InvalidConfigException;

/**
 * Модель документа "Установка меню"
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
class SetMenuForm extends Form
{
    /**
     * @var integer
     */
    public $menu_id;

    /**
     * @var integer
     */
    public $menu_cycle_id;

    /**
     * @var integer
     */
    public $week_day_id;

    public function getName()
    {
        return 'Установка меню и выходных';
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
            'menu_id' => 'Меню',
            'menu_cycle_id' => 'Цикличность меню',
            'week_day_id' => 'День недели',
        ]);
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getMenu()
    {
        return Menu::find()->select('name')->indexBy('id')->column();
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getMenuCycle()
    {
        return MenuCycle::find()->select('name')->indexBy('id')->column();
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getWeekDay()
    {
        return WeekDay::find()->select('name')->indexBy('id')->column();
    }
}