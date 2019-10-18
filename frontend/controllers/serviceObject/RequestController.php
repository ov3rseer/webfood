<?php

namespace frontend\controllers\serviceObject;

use common\components\DateTime;
use common\helpers\ArrayHelper;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use common\models\enum\DocumentStatus;
use common\models\reference\ServiceObject;
use common\models\reference\Product;
use common\models\tablepart\ServiceObjectContract;
use common\models\tablepart\RequestDate;
use Exception;
use frontend\controllers\FrontendModelController;
use frontend\models\serviceObject\RequestForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Контроллер для форм "Предварительная заявка" и "Корректировка заявки"
 */
class RequestController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\RequestForm';

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
                        'actions' => ['index', 'save-request-table'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param $model
     * @return array|Response
     * @throws Exception
     */
    public function getBeginAndEndWeek($model)
    {
        $beginWeek = null;
        $endWeek = null;
        $currentDate = (new DateTime('now'))->format('d-m-Y H:i:s');

        // Работает
        if ($model->scenario == RequestForm::SCENARIO_PRELIMINARY) {
            $thursdayDate = (new DateTime('thursday this week'))->setTime(13, 0)->format('d-m-Y H:i:s');
            if ($currentDate < $thursdayDate) {
                $beginWeek = new DateTime('monday next week');
                $endWeek = clone $beginWeek;
                $endWeek->modify('+ 5 days');
            } else {
                Yii::$app->session->setFlash('info', 'Создание предварительной заявки на следующую неделю доступно не позднее 13:00 четверга этой недели.');
            }
        }

        // Корректировка
        if ($model->scenario == RequestForm::SCENARIO_CORRECTION) {
            $mondayDate = (new DateTime('monday this week'))->setTime(0, 0)->format('d-m-Y H:i:s');
            $thursdayDate = (new DateTime('thursday this week'))->setTime(11, 0)->format('d-m-Y H:i:s');
            $fridayDate = (new DateTime('friday this week'))->setTime(11, 0)->format('d-m-Y H:i:s');
            if ($mondayDate < $currentDate && $currentDate < $thursdayDate) {
                $beginWeek = new DateTime('monday this week');
                $endWeek = clone $beginWeek;
                $endWeek->modify('+ 5 days');
            } elseif ($currentDate < $fridayDate) {
                $beginWeek = new DateTime('monday next week');
                $endWeek = clone $beginWeek;
                $endWeek->modify('+ 5 days');
            } else {
                Yii::$app->session->setFlash('info', 'Корректировка заявки недоступна начиная с 11:00 пятницы и в выходные дни (Сб, Вс).');
            }
        }
        return ['beginWeek' => $beginWeek, 'endWeek' => $endWeek];
    }

    /**
     * @return array|string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex()
    {
        /** @var RequestForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());

        $contractTypeId = $requestData['contractTypeId'] ?? null;
        $action = $requestData['action'] ?? null;
        if (in_array($action, [RequestForm::SCENARIO_PRELIMINARY, RequestForm::SCENARIO_CORRECTION]) && $contractTypeId) {
            $model->scenario = $action;
        } else {
            Yii::$app->session->setFlash('info', 'Вы не выбрали тип заявки.');
            return $this->goHome();
        }

        $userId = Yii::$app->user && Yii::$app->user->id ? Yii::$app->user->id : null;
        if ($userId) {
            /** @var ServiceObject $serviceObject */
            $serviceObject = ServiceObject::findOne(['user_id' => $userId, 'is_active' => true]);

            if (!$serviceObject) {
                Yii::$app->session->setFlash('info', 'Вы не являетесь объектом обслуживания.');
                return $this->goHome();
            }

            $contracts = [];
            $model->service_object_id = $serviceObject->id;
            $model->service_object = (string)$serviceObject . ' (' . $serviceObject->service_object_code . ')';
            if ($serviceObject->serviceObjectContracts) {
                foreach ($serviceObject->serviceObjectContracts as $serviceObjectContract) {
                    if ($serviceObjectContract->contract && $serviceObjectContract->contract->contract_type_id == $contractTypeId) {
                        $contracts[$serviceObjectContract->contract_id] = $serviceObjectContract->contract . ' : ' . $serviceObjectContract->address;
                    }
                }
            }
        }

        if (empty($contracts)) {
            Yii::$app->session->setFlash('info', 'У вас нет активных договоров.');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $model->load($requestData);
        $week = $this->getBeginAndEndWeek($model);

        $requestTable = $model->getRequestTable($week);
        $dataProvider = new ArrayDataProvider(['allModels' => $requestTable]);

        $columns = [];
        if (!empty($requestTable)) {
            $columns = $model->getColumns($week);
        }

        $model->validate();
        return $this->renderUniversal('@frontend/views/service-object/request/index', ['model' => $model, 'dataProvider' => $dataProvider, 'columns' => $columns, 'contracts' => $contracts]);
    }

    /**
     * @return void
     * @throws InvalidConfigException
     * @throws UserException
     * @throws Exception
     */
    public function actionSaveRequestTable()
    {
        /** @var RequestForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $scenario = $requestData['scenario'] ?: null;
        if ($scenario) {
            $model->scenario = $scenario;
            $model->load($requestData);

            $week = $this->getBeginAndEndWeek($model);

            $request = null;
            $serviceObjectContract = null;
            if (isset($week['beginWeek']) && isset($week['endWeek'])) {
                /** @var ServiceObjectContract $serviceObjectContract */
                $serviceObjectContract = ServiceObjectContract::findOne(['contract_id' => $model->contract_id]);
                /** @var Request $request */
                $request = Request::find()
                    ->alias('r')
                    ->innerJoin(RequestDate::tableName() . ' AS rd', 'r.id = rd.parent_id')
                    ->andWhere(['r.service_object_id' => $model->service_object_id, 'r.contract_id' => $model->contract_id, 'r.status_id' => DocumentStatus::DRAFT])
                    ->andWhere(['between', 'rd.week_day_date', $week['beginWeek'], $week['endWeek']])
                    ->with('requestDates')
                    ->one();

                if (!$request && $serviceObjectContract) {
                    $request = new Request();
                    $request->service_object_id = $model->service_object_id;
                    $request->contract_id = $model->contract_id;
                    $request->status_id = DocumentStatus::DRAFT;
                    $request->address = $serviceObjectContract->address;
                    $request->service_object_code = $serviceObjectContract->parent->service_object_code;
                    $request->contract_code = $serviceObjectContract->contract->contract_code;
                    $request->save();
                }

                if ($request) {
                    foreach ($model->productQuantities as $productId => $productQuantityByDate) {
                        $product = Product::findOne(['id' => $productId]);
                        foreach ($productQuantityByDate as $date => $quantities) {
                            $requestDate = RequestDate::findOne(['parent_id' => $request->id, 'week_day_date' => $date]);
                            if (!$requestDate) {
                                $requestDate = new RequestDate();
                                $requestDate->parent_id = $request->id;
                                $requestDate->week_day_date = $date;
                                $requestDate->save();
                            }

                            $requestDateProduct = RequestDateProduct::findOne(['request_date_id' => $requestDate->id, 'product_id' => $product->id]);
                            if (!$requestDateProduct) {
                                $requestDateProduct = new RequestDateProduct();
                                $requestDateProduct->request_date_id = $requestDate->id;
                                $requestDateProduct->product_id = $product->id;
                                $requestDateProduct->unit_id = $product->unit_id;
                            }
                            if ($scenario == RequestForm::SCENARIO_PRELIMINARY && !empty($quantities['planned_quantity'])) {
                                $requestDateProduct->planned_quantity = $quantities['planned_quantity'];
                                $requestDateProduct->current_quantity = $quantities['planned_quantity'];
                                $requestDateProduct->save();
                            }

                            if ($scenario == RequestForm::SCENARIO_CORRECTION && !empty($quantities['current_quantity'])) {
                                if ($requestDateProduct->isNewRecord) {
                                    $requestDateProduct->planned_quantity = 0;
                                    $requestDateProduct->current_quantity = $quantities['current_quantity'];
                                }
                                if (!$requestDateProduct->isNewRecord) {
                                    if ($requestDateProduct->planned_quantity == 0 || ($quantities['current_quantity'] <= $requestDateProduct->planned_quantity * 1.1
                                            && $quantities['current_quantity'] >= $requestDateProduct->planned_quantity * 0.9)) {
                                        $requestDateProduct->current_quantity = isset($quantities['current_quantity']) ? $quantities['current_quantity'] : 0;
                                    }
                                }
                                $requestDateProduct->save();
                            }
                        }
                    }
                    Yii::$app->session->setFlash('success', 'Заявка успешно сформирована/скорректирована');
                }
            }
        }
    }
}