<?php

namespace frontend\controllers\serviceObject;

use common\components\DateTime;
use common\helpers\ArrayHelper;
use common\models\enum\DayType;
use common\models\reference\SetMenu;
use common\models\register\registerConsolidate\Weekend;
use Exception;
use frontend\controllers\FrontendModelController;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
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
     */
    public function actionRenderCalendar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var Weekend[] $weekends */
        $weekends = Weekend::find()->all();
        $events = [];
        foreach ($weekends as $weekend) {
            $event = new Event();
            $event->id = $weekend;
            $event->title = $weekend->dayType->name;
            $event->backgroundColor = 'red';
            $event->start = ($weekend->date)->format('Y-m-d');
            $events[] = $event;
        }
        return $events;
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