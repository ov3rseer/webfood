<?php

namespace frontend\controllers\serviceObject;

use backend\widgets\ActiveForm;
use common\components\DateTime;
use common\helpers\ArrayHelper;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use common\models\reference\Contract;
use common\models\reference\Product;
use common\models\reference\ServiceObject;
use common\models\tablepart\ContractProduct;
use common\models\tablepart\RequestDate;
use common\models\tablepart\ServiceObjectContract;
use frontend\controllers\FrontendModelController;
use frontend\models\serviceObject\request\RequestTable;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Response;

class RequestTableController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\RequestTable';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $result = parent::actions();
        unset($result['index']);
        return $result;
    }

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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return array|string
     * @throws InvalidConfigException
     * @throws UserException
     * @throws \Exception
     */
    public function indexAction(){
        /** @var RequestTable $model */
        $model = new $this->modelClass();
        $action = Yii::$app->request->get('action');
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $currentDate = date('d-m-Y H:i');

        $userId = Yii::$app->user->id;
        $serviceObjectId = $requestData['serviceObjectName'];
        $contractId = $requestData['contractCode'];
        $contractTypeId = $requestData['contractTypeId'];
        $action = $action ?: $requestData['action'];

        $array = [];
        $weekDayDateMap = [];

        switch ($action) {
            case 'preliminary_request': {
                $thursdayDate = new DateTime('thursday this week');
                $thursdayDate = $thursdayDate->format('d-m-Y 13:00');

                if (strtotime($currentDate) < strtotime($thursdayDate)) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday next week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($serviceObjectId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                }

                if (isset($requestDateProducts)) {
                    if ($requestDateProducts) {
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
                    //Yii::$app->session->setFlash('info', 'Вы создали заявку для этого объекта обслуживания и договора.<br/>Перейдите в раздел корректировки заявки.');
                } else {
                    Yii::$app->session->setFlash('info', 'Предварительная заявка доступна не позднее 13:00 четверга этой недели.');
                }

                break;
            }
            case 'correction_request': {
                $thursdayDate = new DateTime('thursday this week');
                $thursdayDate = $thursdayDate->format('d-m-Y 13:00');

                if (strtotime($currentDate) < strtotime($thursdayDate)) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday this week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($serviceObjectId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                }

                if (!isset($requestDateProducts) || (isset($requestDateProducts) && !$requestDateProducts)) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday next week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($serviceObjectId, $contractId, $weekDayDateMap);
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
                    Yii::$app->session->setFlash('info', 'Не существует заявки для этого объекта обслуживания и договора.<br/>Перейдите в раздел предварительной заявки.');
                }
                break;
            }
            case 'request-table': {
                $productQuantities = $model->getProductQuantities($requestData['fields']);
                $requestWeekDateMap = $model->getRequestWeekDateMapByProductQuantities($productQuantities);

                $thursdayDate = new DateTime('thursday this week');
                $thursdayDate = $thursdayDate->format('d-m-Y 13:00');

                if (strtotime($currentDate) < strtotime($requestWeekDateMap[0]) && strtotime($currentDate) < strtotime($thursdayDate)) {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday next week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($serviceObjectId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    $allFields = 1;
                } else {
                    $weekDayDateMap = $model->getWeekDayDateMap('monday this week');
                    $requestDatesIdMap = $model->findRequestDatesIdMap($serviceObjectId, $contractId, $weekDayDateMap);
                    $requestDateProducts = $model->getRequestDateProductsByRequestDatesId(array_keys($requestDatesIdMap));
                    $allFields = 0;
                }

                if ($userId && $contractId && $serviceObjectId) {
                    $serviceObjectContract = (new Query())
                        ->select('*')
                        ->from(ServiceObjectContract::tableName().' as soc, '.Contract::tableName().' as c, '.ServiceObject::tableName().' as so')
                        ->where('c.id = soc.contract_id and so.id = soc.parent_id')
                        ->andWhere(['soc.parent_id' => $serviceObjectId, 'soc.contract_id' => $contractId])
                        ->one();

                    $request = Request::findOne(['service_object_id' => $serviceObjectId, 'contract_id' => $contractId, 'contract_type_id' => $contractTypeId]) ?: new Request();
                    $request->service_object_id = $serviceObjectId;
                    $request->contract_id = $contractId;
                    $request->address = $serviceObjectContract['address'];
                    $request->service_object_code = $serviceObjectContract['service_object_code'];
                    $request->contract_code = $serviceObjectContract['contract_code'];
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
        if ($userId && $contractId && $serviceObjectId) {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $array
            ]);
        }

        return $this->renderUniversal('@frontend/views/service-object/request/request-table/index', ['model' => $model, 'dataProvider' => $dataProvider, 'weekDayDateMap' => $weekDayDateMap]);
    }
}