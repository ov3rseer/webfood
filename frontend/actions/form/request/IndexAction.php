<?php

namespace frontend\actions\form\request;

use common\models\reference\Contract;
use common\models\reference\ServiceObject;
use common\models\tablepart\ServiceObjectContract;
use frontend\actions\FrontendModelAction;
use backend\widgets\ActiveForm;
use frontend\models\request\RequestForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\web\Response;

/**
 * Действие для вывода формы
 */
class IndexAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function run()
    {
        /** @var RequestForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $userId = Yii::$app->user->id;
        $contractTypeId = Yii::$app->request->get('contractTypeId');
        $requestData['contractsByServiceObjects'] = [];
        $logic = [];
        if ($userId) {
            /** @var ServiceObject[] $serviceObjects */
            $serviceObjects = ServiceObject::find()->andWhere(['user_id' => $userId, 'is_active' => true])->all();
            if ($serviceObjects) {
                foreach ($serviceObjects as $serviceObject) {
                    $service_object_name = $serviceObject->service_object_code.": ".$serviceObject->name;
                    //$serviceObjectIds[] = $serviceObject->id;
                    $model->service_object_name[$serviceObject->id] = $service_object_name;
                    if (!isset($logic[$serviceObject->id])) $logic[$serviceObject->id] = [];
                    //$model->service_object_code[] = $serviceObject->service_object_code;
                    /** @var Contract[] $contract */
                    $contracts = (new Query())
                        ->select('*')
                        ->from(Contract::tableName().' as c, '.ServiceObjectContract::tableName().' as cc')
                        ->where('c.id = cc.contract_id')
                        ->andWhere(['c.contract_type_id' => $contractTypeId, 'cc.parent_id' => $serviceObject->id])
                        ->all();

                    if ($contracts) {
                        foreach ($contracts as $contract) {
                            $contract_code = $contract['contract_code'].": ".$contract['address'];
                            if (!isset($requestData['contractsByServiceObjects'][$contract['parent_id']])) {
                                $requestData['contractsByServiceObjects'][$contract['parent_id']] = [];
                            }
                            $requestData['contractsByServiceObjects'][$contract['parent_id']][] = $contract;
                            $model->contract_code[$contract['contract_id']] = $contract_code;
                            $logic[$serviceObject->id][$contract['contract_id']] = $contract_code;
                            //$model->address[] = $contract['address'];
                        }
                    } else {
                        Yii::$app->session->setFlash('info', 'У вас нет активных договоров.');
                    }
                }
            } else {
                Yii::$app->session->setFlash('info', 'У вас нет активных объектов обслуживания.');
            }
        }
        $model->logic = $logic;
        if ($model->load($requestData) && $model->validate()) {
            $model->proceed();
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
