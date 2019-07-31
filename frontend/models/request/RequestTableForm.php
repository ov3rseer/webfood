<?php

namespace frontend\models\request;

use common\components\DateTime;
use common\models\document\Request;
use common\models\tablepart\RequestDate;
use frontend\models\FrontendForm;

class RequestTableForm extends FrontendForm
{

    function getWeekDayDateMap() {
        $weekDayDateMap = [];
        $startNextWeek = new DateTime('next monday');
        $endNextWeek = clone $startNextWeek;
        for ($i = 0; $i < 5; $i++) {
            $weekDayDateMap[] = $endNextWeek->format('d-m-Y');
            $endNextWeek->modify('+ 1 days');
        }
        return $weekDayDateMap;
    }

    function getIdMapByResult($result, $field = 'id') {
        $idMap = [];
        foreach ($result as $res) {
            $idMap[$res->id] = $res->{$field};
        }
        return;
    }

    function getProductQuantities($get) {
        foreach ($get as $key => $value) {
            $array = 0;
            if (preg_match("/(planned_quantity)/", $key)) {
                $array = 'planned_quantity';
            } elseif (preg_match("/(current_quantity)/", $key)) {
                $array = 'current_quantity';
            }
            if ($array && $value) {
                $key = explode('_', $key);
                if (!isset($$array[$key[0]])) {
                    $$array[$key[0]] = [];
                }
                $$array[$key[0]][$key[1]] = $value;
            }
        }
    }

    function getRequestsByContractorContract($contractorId, $contractId) {
        return Request::find()->andWhere(['contractor_id' => $contractorId, 'contract_id' => $contractId])->all();
    }

    function getRequestDatesByRequestsAndWeekDayDates($parentIdMap, $weekDayDateMap) {
        return RequestDate::find()->andWhere(['parent_id' => $parentIdMap, 'week_day_date' => $weekDayDateMap])->all();
    }

}