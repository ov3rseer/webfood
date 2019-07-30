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
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\web\Response;

/**
 * Действие для вывода формы
 * @method getParent()
 */
class IndexAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
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

        $currentDate = date('d-m-Y H:i');

        $userId = Yii::$app->user->id;
        $contractorId = $requestData['contractorName'];
        $contractId = $requestData['contractCode'];
        $contractTypeId = $requestData['contractTypeId'];
        $action = $action ?: $requestData['action'];

        $array = [];

        switch ($action) {
            case 'preliminary_request': {
                $thursdayDate = new DateTime('thursday this week');
                $thursdayDate = $thursdayDate->format('d-m-Y 13:00');

                if (strtotime($currentDate) < strtotime($thursdayDate)) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday next week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($contractorId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    //Yii::$app->session->setFlash('info', 'Предварительная заявка доступна не позднее 13:00 четверга этой недели.');
                }

                if (isset($requestDateProducts)) {
                    $contractProducts = ContractProduct::find()->andWhere(['parent_id' => $contractId])->all();
                    foreach ($contractProducts as $contractProduct) {
                        $array[$contractProduct->product_id] = [
                            'product_code' => $contractProduct->product->product_code,
                            'product_name' => $contractProduct->product->name,
                            'product_unit' => $contractProduct->product->unit->name,
                            'quantities'   => [],
                        ];
                    }
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    foreach ($requestDateProducts as $requestDateProduct) {
                        $array[$requestDateProduct->product_id]['quantities'][$requestDatesIdMap[$requestDateProduct->request_date_id]] = [
                            'planned_quantity' => $requestDateProduct->planned_quantity,
                            'current_quantity' => $requestDateProduct->current_quantity,
                        ];
                    }
                    //Yii::$app->session->setFlash('info', 'Вы создали заявку для этого контрагента и договора.<br/>Перейдите в раздел корректировки заявки.');
                } else {
                    $contractProducts = ContractProduct::find()->andWhere(['parent_id' => $contractId])->all();
                    foreach ($contractProducts as $contractProduct) {
                        $array[$contractProduct->product_id] = [
                            'product_code' => $contractProduct->product->product_code,
                            'product_name' => $contractProduct->product->name,
                            'product_unit' => $contractProduct->product->unit->name,
                            'quantities'   => [],
                        ];
                    }
                }

                break;
            };
            case 'correction_request': {
                $weekDayDateMap = $model->getWeekDayDateMap('monday this week');
                $requestDatesIdMap = $model->findRequestDatesIdMap($contractorId, $contractId, $weekDayDateMap);
                $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));

                if (!$requestDateProducts) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday next week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($contractorId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                }

                $contractProducts = ContractProduct::find()->andWhere(['parent_id' => $contractId])->all();

                if (isset($requestDateProducts) && $requestDateProducts) {
                    foreach ($contractProducts as $contractProduct) {
                        $array[$contractProduct->product_id] = [
                            'product_code' => $contractProduct->product->product_code,
                            'product_name' => $contractProduct->product->name,
                            'product_unit' => $contractProduct->product->unit->name,
                            'quantities'   => [],
                        ];
                    }
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    foreach ($requestDateProducts as $requestDateProduct) {
                        $array[$requestDateProduct->product_id]['quantities'][$requestDatesIdMap[$requestDateProduct->request_date_id]] = [
                            'planned_quantity' => $requestDateProduct->planned_quantity,
                            'current_quantity' => $requestDateProduct->current_quantity,
                        ];
                    }
                } else {
                    Yii::$app->session->setFlash('info', 'Не существует заявки для этого контрагента и договора.<br/>Перейдите в раздел предварительной заявки.');
                }
                break;
            };
            case 'request-table': {
                $productQuantities = $model->getProductQuantities($requestData['fields']);
                $requestWeekDateMap = $model->getRequestWeekDateMapByProductQuantities($productQuantities);

                $thursdayDate = new DateTime('thursday this week');
                $thursdayDate = $thursdayDate->format('d-m-Y 13:00');

                if (strtotime($currentDate) < strtotime($requestWeekDateMap[0]) && strtotime($currentDate) < strtotime($thursdayDate)) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday next week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($contractorId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    $allFields = 1;
                } else {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday this week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($contractorId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    $allFields = 0;
                }

                if ($userId && $contractId && $contractorId) {
                    $contractorContract = (new Query())
                        ->select('*')
                        ->from(ContractorContract::tableName().' as cc, '.Contract::tableName().' as c, '.Contractor::tableName().' as ccc')
                        ->where('c.id = cc.contract_id and ccc.id = cc.parent_id')
                        ->andWhere(['cc.parent_id' => $contractorId, 'cc.contract_id' => $contractId])
                        ->one();

                    $request = Request::findOne(['contractor_id' => $contractorId, 'contract_id' => $contractId, 'contract_type_id' => $contractTypeId]) ?: new Request();
                    $request->contractor_id = $contractorId;
                    $request->contract_id = $contractId;
                    $request->address = $contractorContract['address'];
                    $request->contractor_code = $contractorContract['contractor_code'];
                    $request->contract_code = $contractorContract['contract_code'];
                    $request->contract_type_id = $contractTypeId;
                    $request->save();

                    foreach ($productQuantities as $productCode => $productQuantity) {
                        $product = Product::findOne(['product_code' => $productCode]);

                        foreach ($productQuantity as $productQuantityDate => $quantities) {
                            $productQuantityDateFormat = new DateTime($productQuantityDate);
                            $productQuantityDateFormat->modify('-1 day')->format('d-m-Y 11:00');

                            if (strtotime($currentDate) <= strtotime($productQuantityDateFormat)) {
                                $requestDate = RequestDate::findOne(['parent_id' => $request->id, 'week_day_date' => $productQuantityDate]) ?: new RequestDate();
                                $requestDate->parent_id = $request->id;
                                $requestDate->week_day_date = $productQuantityDate;
                                $requestDate->save();

                                $requestDateProduct = RequestDateProduct::findOne(['request_date_id' => $requestDate->id, 'product_id' => $product->id]);

                                if ($requestDateProduct) {
                                    if ($allFields) {
                                        $requestDateProduct->planned_quantity = isset($quantities['planned_quantity']) ? $quantities['planned_quantity'] : 0;
                                        $requestDateProduct->current_quantity = isset($quantities['current_quantity']) ? $quantities['current_quantity'] : 0;
                                        $requestDateProduct->save();
                                    } else {
                                        if ($quantities['current_quantity'] <= $requestDateProduct->planned_quantity * 1.1 && $quantities['current_quantity'] >= $requestDateProduct->planned_quantity * 0.9) {
                                            $requestDateProduct->current_quantity = isset($quantities['current_quantity']) ? $quantities['current_quantity'] : 0;
                                            $requestDateProduct->save();
                                        }
                                    }
                                } else {
                                    $requestDateProduct = new RequestDateProduct();
                                    $requestDateProduct->request_date_id = $requestDate->id;
                                    $requestDateProduct->product_id = $product->id;
                                    $requestDateProduct->unit_id = $product->unit_id;
                                    $requestDateProduct->planned_quantity = isset($quantities['planned_quantity']) ? $quantities['planned_quantity'] : 0;
                                    $requestDateProduct->current_quantity = isset($quantities['current_quantity']) ? $quantities['current_quantity'] : 0;
                                    $requestDateProduct->save();
                                }
                            }
                        }
                    }

                    $contractProducts = ContractProduct::find()->andWhere(['parent_id' => $contractId])->all();

                    foreach ($contractProducts as $contractProduct) {
                        $array[$contractProduct->product_id] = [
                            'product_code' => $contractProduct->product->product_code,
                            'product_name' => $contractProduct->product->name,
                            'product_unit' => $contractProduct->product->unit->name,
                            'quantities'   => [],
                        ];
                    }

                    if (isset($requestDateProducts) && $requestDateProducts) {
                        $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                        foreach ($requestDateProducts as $requestDateProduct) {
                            $array[$requestDateProduct->product_id]['quantities'][$requestDatesIdMap[$requestDateProduct->request_date_id]] = [
                                'planned_quantity' => $requestDateProduct->planned_quantity,
                                'current_quantity' => $requestDateProduct->current_quantity,
                            ];
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
            $dataProvider = new ArrayDataProvider([
                'allModels' => $array
            ]);
        }

        return $this->controller->renderUniversal($this->viewPath, ['model' => $model, 'dataProvider' => $dataProvider, 'weekDayDateMap' => $weekDayDateMap]);
    }
}
