<?php

namespace frontend\models\request;

use common\components\DateTime;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use common\models\reference\Product;
use common\models\tablepart\RequestDate;
use frontend\models\FrontendForm;

class RequestTableForm extends FrontendForm
{

    function getWeekDayDateMap($time = 'now') {
        $weekDayDateMap = [];
        $startNextWeek = new DateTime($time);
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
        return $idMap;
    }

    function getIdMapByResultForDates($result, $field) {
        $idMap = [];
        foreach ($result as $res) {
            $idMap[$res->id] = $res->{$field}->format('d-m-Y');
        }
        return $idMap;
    }

    function getProductQuantities($get) {
        $productQuantities = [];
        foreach ($get as $key => $value) {
            $quantityType = 0;
            if (preg_match("/(planned_quantity)/", $key)) {
                $quantityType = 'planned_quantity';
            } elseif (preg_match("/(current_quantity)/", $key)) {
                $quantityType = 'current_quantity';
            }
            if ($quantityType && $value) {
                $key = explode('_', $key);
                if (!isset($productQuantities[$key[0]])) {
                    $productQuantities[$key[0]] = [];
                }
                if (!isset($productQuantities[$key[0]][$key[1]])) {
                    $productQuantities[$key[0]][$key[1]] = [];
                }
                $productQuantities[$key[0]][$key[1]][$quantityType] = $value;
            }
        }
        return $productQuantities;
    }

    function findRequestDatesIdMap($contractorId, $contractId, $weekDayDateMap) {
        $requestDatesIdMap = [];
        $requests = $this->getRequestsByContractorContract($contractorId, $contractId);
        if ($requests) {
            $requestsIdMap = $this->getIdMapByResult($requests);
            $requestDates = $this->getRequestDatesByRequestsAndWeekDayDates($requestsIdMap, $weekDayDateMap);
            if ($requestDates) {
                $requestDatesIdMap = $this->getIdMapByResultForDates($requestDates, 'week_day_date');
            }
        }
        return $requestDatesIdMap;
    }

    function getRequestsByContractorContract($contractorId, $contractId) {
        return Request::find()->andWhere(['contractor_id' => $contractorId, 'contract_id' => $contractId])->all();
    }

    function getRequestDatesByRequestsAndWeekDayDates($parentIdMap, $weekDayDateMap) {
        return RequestDate::find()->andWhere(['parent_id' => $parentIdMap, 'week_day_date' => $weekDayDateMap])->all();
    }

    function getRequestDateProductsByRequestDatesId($requestDatesIdMap) {
        return RequestDateProduct::find()->andWhere(['request_date_id' => $requestDatesIdMap])->all();
    }

    function getProductsByCode($codes) {
        return Product::find()->andWhere(['product_code' => $codes])->all();
    }

}