<?php

namespace frontend\models\serviceObject;

use common\models\enum\MenuCycle;
use common\models\enum\WeekDay;
use common\models\form\SystemForm;
use common\models\reference\Menu;
use common\models\reference\SetMenu;
use yii\base\InvalidConfigException;
use yii\base\UserException;

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
class SetMenuForm extends SystemForm
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'Установка меню и выходных дней';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['menu_id', 'menu_cycle_id', 'week_day_id'], 'integer'],
            [['menu_id', 'menu_cycle_id', 'week_day_id'], 'required'],
            [['menu_cycle_id'], 'validateMenuCycle'],
        ]);
    }

    /**
     * @throws InvalidConfigException
     */
    public function validateMenuCycle()
    {
        $menuCycles = [MenuCycle::WEEKLY, MenuCycle::ODD_WEEKS, MenuCycle::EVEN_WEEKS];
        switch ($this->menu_cycle_id) {
            case MenuCycle::ODD_WEEKS:
                $menuCycles = [MenuCycle::ODD_WEEKS, MenuCycle::WEEKLY];
                break;
            case MenuCycle::EVEN_WEEKS:
                $menuCycles = [MenuCycle::EVEN_WEEKS, MenuCycle::WEEKLY];
                break;
            default:
                break;
        }
        $setMenu = SetMenu::find()
            ->andWhere([
                'week_day_id' => $this->week_day_id,
                'menu_cycle_id' => $menuCycles,
                'is_active' => true,
            ])
            ->one();
        if ($setMenu) {
            $this->addError('menu_cycle_id', 'Для того чтобы установить другую цикличность, вам необходимо удалить текущее меню с цикличностью "' . $setMenu->menuCycle . '" в день недели "' . $setMenu->weekDay . '".');
        }
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

    /**
     * @return mixed|void
     * @throws InvalidConfigException
     * @throws UserException
     */
    public function proceed()
    {
        $setMenu = SetMenu::find()->andWhere(['week_day_id' => $this->week_day_id, 'menu_cycle_id' => $this->menu_cycle_id])->one();
        if (!$setMenu) {
            $setMenu = new SetMenu();
        }
        $setMenu->menu_id = $this->menu_id;
        $setMenu->is_active = true;
        $setMenu->menu_cycle_id = $this->menu_cycle_id;
        $setMenu->week_day_id = $this->week_day_id;
        $setMenu->save();
    }
}