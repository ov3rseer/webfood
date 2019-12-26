<?php

namespace frontend\actions\setMenu;

use common\components\DateTime;
use common\models\enum\DayType;
use common\models\register\registerConsolidate\Weekend;
use frontend\actions\FrontendModelAction;
use Yii;
use yii\base\UserException;
use yii\db\StaleObjectException;

class EditMenuAction extends FrontendModelAction
{
    /**
     * @return bool
     * @throws \Throwable
     * @throws UserException
     * @throws StaleObjectException
     */
    public function run()
    {
        $beginDay = Yii::$app->request->post('beginDay');
        $endDay = Yii::$app->request->post('endDay');
        if ($beginDay) {
            $beginDay = new DateTime($beginDay);
            do {
                $weekend = Weekend::findOne(['day_type_id' => DayType::WEEKEND, 'date' => $beginDay,]);
                if (!$weekend) {
                    $weekend = new Weekend();
                    $weekend->date = $beginDay;
                    $weekend->day_type_id = DayType::WEEKEND;
                    $weekend->save();
                } else {
                    $weekend->delete();
                }
                $beginDay->modify('+ 1 days');
            } while ($beginDay < $endDay);
            return true;
        }
        return false;
    }
}