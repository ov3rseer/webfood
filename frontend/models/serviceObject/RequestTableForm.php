<?php

namespace frontend\models\serviceObject;

use common\components\DateTime;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use common\models\reference\Product;
use common\models\tablepart\RequestDate;
use Exception;
use frontend\models\FrontendForm;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

class RequestTableForm extends FrontendForm
{

    /**
     * @param string $time
     * @return array
     * @throws Exception
     */
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

    /**
     * @param $result
     * @param string $field
     * @return array
     */
    function getIdMapByResult($result, $field = 'id') {
        $idMap = [];
        foreach ($result as $res) {
            $idMap[$res->id] = $res->{$field};
        }
        return $idMap;
    }

    /**
     * @param $result
     * @param $field
     * @return array
     */
    function getIdMapByResultForDates($result, $field) {
        $idMap = [];
        foreach ($result as $res) {
            $idMap[$res->id] = $res->{$field}->format('d-m-Y');
        }
        return $idMap;
    }

    /**
     * @param $get
     * @return array
     */
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

    /**
     * @param $productQuantities
     * @return array
     * @throws Exception
     */
    function getRequestWeekDateMapByProductQuantities($productQuantities) {
        $requestOneDayDate = array_keys($productQuantities[array_keys($productQuantities)[0]])[0];
        $date = new DateTime($requestOneDayDate);
        $dayOfWeek = $date->format("w");
        $sundayDate = $date->modify("-$dayOfWeek days");
        $weekDayDateMap = [
            $sundayDate->modify('+1 day')->format('d-m-Y 11:00'),
            $sundayDate->modify('+2 day')->format('d-m-Y 11:00'),
            $sundayDate->modify('+3 day')->format('d-m-Y 11:00'),
            $sundayDate->modify('+4 day')->format('d-m-Y 11:00'),
            $sundayDate->modify('+5 day')->format('d-m-Y 11:00'),
        ];
        return $weekDayDateMap;
    }

    /**
     * @param $serviceObjectId
     * @param $contractId
     * @param $weekDayDateMap
     * @return array
     * @throws InvalidConfigException
     * @throws InvalidConfigException
     */
    function findRequestDatesIdMap($serviceObjectId, $contractId, $weekDayDateMap) {
        $requestDatesIdMap = [];
        $requests = $this->getRequestsByServiceObjectContract($serviceObjectId, $contractId);
        if ($requests) {
            $requestsIdMap = $this->getIdMapByResult($requests);
            $requestDates = $this->getRequestDatesByRequestsAndWeekDayDates($requestsIdMap, $weekDayDateMap);
            if ($requestDates) {
                $requestDatesIdMap = $this->getIdMapByResultForDates($requestDates, 'week_day_date');
            }
        }
        return $requestDatesIdMap;
    }

    /**
     * @param $serviceObjectId
     * @param $contractId
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     */
    function getRequestsByServiceObjectContract($serviceObjectId, $contractId) {
        return Request::find()->andWhere(['service_object_id' => $serviceObjectId, 'contract_id' => $contractId])->all();
    }

    /**
     * @param $parentIdMap
     * @param $weekDayDateMap
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     */
    function getRequestDatesByRequestsAndWeekDayDates($parentIdMap, $weekDayDateMap) {
        return RequestDate::find()->andWhere(['parent_id' => $parentIdMap, 'week_day_date' => $weekDayDateMap])->all();
    }

    /**
     * @param $requestDatesIdMap
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     */
    function getRequestDateProductsByRequestDatesId($requestDatesIdMap) {
        return RequestDateProduct::find()->andWhere(['request_date_id' => $requestDatesIdMap])->all();
    }

    /**
     * @param $codes
     * @return array|ActiveRecord[]
     * @throws InvalidConfigException
     */
    function getProductsByCode($codes) {
        return Product::find()->andWhere(['product_code' => $codes])->all();
    }

}