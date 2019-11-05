<?php

namespace frontend\controllers\serviceObject;

use common\components\DateTime;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\models\enum\DayType;
use common\models\enum\MenuCycle;
use common\models\reference\SetMenu;
use common\models\register\registerConsolidate\Weekend;
use Exception;
use frontend\controllers\FrontendModelController;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Response;
use yii2fullcalendar\models\Event;

class SetMenuController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\SetMenuForm';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'render-calendar', 'add-weekend', 'delete-weekend'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/set-menu/index',
            ],
        ]);
    }

    /**
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionRenderCalendar()
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
        $setMenus = SetMenu::find()->all();
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
            $event->className = $weekend;
            $event->title = Html::encode($weekend->dayType->name);
            $event->backgroundColor = 'red';
            $event->start = $weekend->date->format('Y-m-d');
            $eventsByDate[$weekend->date->format('Y-m-d')] = $event;
        }
        return $events = array_values($eventsByDate);
    }

    /**
     * @throws Exception
     */
    public function actionAddWeekend()
    {
        $requestData = Yii::$app->request->post();
        if (isset($requestData['beginDay']) && isset($requestData['endDay'])) {
            $end = new DateTime($requestData['endDay']);
            $start = new DateTime($requestData['beginDay']);
            while ($start < $end) {
                $weekend = Weekend::findOne(['date' => $start]);
                if (!$weekend) {
                    $weekend = new Weekend();
                    $weekend->date = $start;
                    $weekend->day_type_id = DayType::WEEKEND;
                    $weekend->save();
                }
                $start->modify('+ 1 days');
            }
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function actionDeleteWeekend()
    {
        $requestData = Yii::$app->request->post();
        if (isset($requestData['date'])) {
            $date = new DateTime($requestData['date']);
            $weekend = Weekend::findOne(['date' => $date]);
            if ($weekend) {
                $weekend->delete();
            }
        }
        return true;
    }
}