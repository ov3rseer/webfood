<?php

namespace frontend\actions\form\requesttable;

use common\components\DateTime;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use common\models\reference\Contract;
use common\models\reference\Contractor;
use common\models\reference\Product;
use common\models\tablepart\ContractorContract;
use common\models\tablepart\ContractProduct;
use common\models\tablepart\RequestDate;
use frontend\actions\FrontendModelAction;
use backend\widgets\ActiveForm;
use frontend\models\request\RequestTableForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Response;
use function GuzzleHttp\Promise\all;

/**
 * Действие для вывода формы
 * @method getParent()
 */
class IndexAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /** @var RequestTableForm $model */
        $model = new $this->modelClass();
        $action = Yii::$app->request->get('action');
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $weekDayDateMap = $model->getWeekDayDateMap();

        $userId = Yii::$app->user->id;
        $contractorId = Yii::$app->request->get('contractorName');
        $contractId = Yii::$app->request->get('contractCode');
        $contractTypeId = Yii::$app->request->get('contractTypeId');;

        $requests = $model->getRequestsByContractorContract($contractorId, $contractId);
        if ($requests) {
            $requestsIdMap = $model->getIdMapByResult($requests);
            $requestDates = $model->getRequestDatesByRequestsAndWeekDayDates($requestsIdMap, $weekDayDateMap);
            if ($requestDates) {
                $requestDatesIdMap = $model->getIdMapByResult($requestDates, 'week_day_date');
                $requestDateProducts = RequestDateProduct::find()->andWhere(['request_date_id' => array_keys($requestDatesIdMap)]);
            }
        }

        switch ($action) {
            case 'request': break;
            case 'request-table':
            {
                if ($userId && $contractId && $contractorId) {
                    $planned_quantity = [];
                    $current_quantity = [];
                    foreach (Yii::$app->request->get() as $key => $value) {
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

                    $products = Product::find()->andWhere(['product_code' => array_merge(array_keys($current_quantity), array_keys($planned_quantity))])->all();
                    //$requestDateProducts = $requestDateProducts->all();

                    if (isset($requestDateProducts) && $requestDateProducts) {
                        foreach ($requestDatesIdMap as $requestDateId) {
                            $queryByDate = clone $requestDateProducts;
                            $queryByDate->andWhere(['request_date_id' => $requestDateProducts]);

                            if ($queryByDate) {
                                foreach ($products as $product) {
                                    $queryByProduct = clone $queryByDate;
                                    $queryByProduct->andWhere(['product_id' => $product->id])->one();
                                    if ($queryByProduct) {
                                        $queryByProduct->current_quantity = $current_quantity[$products{$i}->product_code][$requestDatesIdMap[$queryByProduct->request_date_id]] ?: 0;
                                    }
                                }
                            }
                        }
                        /*$i = 0;
                        foreach ($requestDateProducts as $requestDateProduct) {
                            foreach ($fieldsMapForRequestDateProduct as $key => $value) {
                                $requestDateProduct->{$key} = $products{$i}->{$value};
                            }

                            $k = 0;
                            for ($k = 0; $k < count($weekDayDateMap); $k++) {
                                if ($requestDateProduct->request_date_id == $requestDatesIdMap[$k]) {
                                    $requestDateProduct->current_quantity = $current_quantity[$products{$i}->product_code][$k]['value'] ?: 0;
                                    $requestDateProduct->save();
                                }
                            }
                            $i++;
                        }*/
                    } else {
                        $contractorContract = (new Query())
                            ->select('*')
                            ->from(ContractorContract::tableName().' as cc, '.Contract::tableName().' as c, '.Contractor::tableName().' as ccc')
                            ->where('c.id = cc.contract_id and ccc.id = cc.parent_id')
                            ->andWhere(['cc.parent_id' => $contractorId, 'cc.contract_id' => $contractId])
                            ->one();
                            /*ContractorContract::find()
                            ->alias('cc')
                            ->innerJoin(Contract::tableName().' AS c ON cc.contract_id = c.id')
                            ->andWhere(['cc.parent_id' => $contractorId, 'cc.contract_id' => $contractId])->one();*/
                        $request = new Request();
                        $request->contractor_id = $contractorId;
                        $request->contract_id = $contractId;
                        $request->address = $contractorContract['address'];
                        $request->contractor_code = $contractorContract['contractor_code'];
                        $request->contract_code = $contractorContract['contract_code'];
                        $request->contract_type_id = $contractTypeId;
                        $request->save();

                        $i = 0;
                        foreach ($weekDayDateMap as $weekDayDate) {
                            $requestDate = new RequestDate();
                            $requestDate->parent_id = $request->id;
                            $requestDate->week_day_date = $weekDayDate;
                            $requestDate->save();

                            foreach ($products as $product) {
                                $requestDateProduct = new RequestDateProduct();
                                $requestDateProduct->request_date_id = $requestDate->id;
                                $requestDateProduct->product_id = $product->id;
                                $requestDateProduct->unit_id = $product->unit_id;
                                $requestDateProduct->planned_quantity = $planned_quantity[$product->product_code][$requestDatesIdMap[$requestDate->id]] ?: 0;
                                $requestDateProduct->current_quantity = $current_quantity[$product->product_code][$requestDatesIdMap[$requestDate->id]] ?: 0;
                                $requestDateProduct->save();
                            }

                            $i++;
                        }
                    }
                }
            }
        }

        if ($model->load($requestData) && $model->validate()) {
            $model->proceed();
        }

        $dataProvider = [];
        if ($userId && $contractId && $contractorId) {
            $query = isset($requestDateProducts) && $requestDateProducts ? $requestDateProducts : ContractProduct::find()->andWhere(['parent_id' => $contractId]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query
            ]);
        }

        return $this->controller->renderUniversal($this->viewPath, ['model' => $model, 'dataProvider' => $dataProvider]);
    }
}
