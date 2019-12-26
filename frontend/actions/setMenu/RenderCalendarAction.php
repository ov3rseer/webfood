<?php

namespace frontend\actions\setMenu;

use common\components\DateTime;
use common\helpers\StringHelper;
use common\models\enum\MenuCycle;
use common\models\reference\SetMenu;
use common\models\register\registerConsolidate\Weekend;
use frontend\actions\FrontendModelAction;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\web\Response;
use yii2fullcalendar\models\Event;

class RenderCalendarAction extends FrontendModelAction
{
    /**
     * @return array
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $start = Yii::$app->request->get('start') ?? null;
        $end = Yii::$app->request->get('end') ?? null;
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);

        $daysByWeekDay = [];
        while ($startDate < $endDate) {
            $cloneDate = clone $startDate;
            if ($startDate->format('N')) {
                $daysByWeekDay[$startDate->format('N')][] = $cloneDate;
            }
            $startDate->modify('+1 days');
        }

        $eventsByDate = [];
        /** @var SetMenu[] $setMenus */
        $setMenus = SetMenu::find()->andWhere(['is_active' => true])->all();
        foreach ($setMenus as $setMenu) {
            foreach ($daysByWeekDay[$setMenu->week_day_id] as $day) {
                $menuCycleId = null;
                if ($day->format('W') % 2 == 0) {
                    $menuCycleId = MenuCycle::EVEN_WEEKS;
                } else {
                    $menuCycleId = MenuCycle::ODD_WEEKS;
                }
                if (in_array($setMenu->menu_cycle_id, [$menuCycleId, MenuCycle::WEEKLY])) {
                    $event = new Event();
                    $event->id = StringHelper::generateFakeId();
                    $event->nonstandard = $setMenu;
                    $event->description = 'menu';
                    $event->title = Html::encode($setMenu->menu);
                    $event->backgroundColor = 'blue';
                    $event->start = $day->format('Y-m-d');
                    $eventsByDate[$day->format('Y-m-d')] = $event;
                }
            }
        }

        /** @var Weekend[] $weekends */
        $weekends = Weekend::find()->all();
        foreach ($weekends as $weekend) {
            $event = new Event();
            $event->id = StringHelper::generateFakeId();
            $event->nonstandard = $weekend;
            $event->description = 'weekend';
            $event->title = Html::encode($weekend->dayType->name);
            $event->backgroundColor = 'red';
            $event->start = $weekend->date->format('Y-m-d');
            $eventsByDate[$weekend->date->format('Y-m-d')] = $event;
        }
        return $events = array_values($eventsByDate);
    }
}