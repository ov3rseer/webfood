<?php

namespace frontend\actions\form\request;

use common\models\reference\Contract;
use common\models\reference\Contractor;
use common\models\tablepart\ContractorContract;
use frontend\actions\FrontendModelAction;
use backend\widgets\ActiveForm;
use frontend\models\request\RequestForm;
use Yii;
use yii\db\Query;
use yii\web\Response;

/**
 * Действие для вывода формы
 */
class IndexAction extends FrontendModelAction
{
    /**
     * @inheritdoc
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
        $requestData['contractsByContractors'] = [];
        $logic = [];
        if ($userId) {
            /** @var Contractor[] $contractors */
            $contractors = Contractor::find()->andWhere(['user_id' => $userId])->all();
            if ($contractors) {
                foreach ($contractors as $contractor) {
                    $contractor_name = $contractor->contractor_code.": ".$contractor->name;
                    //$contractorIds[] = $contractor->id;
                    $model->contractor_name[$contractor->id] = $contractor_name;
                    if (!isset($logic[$contractor->id])) $logic[$contractor->id] = [];
                    //$model->contractor_code[] = $contractor->contractor_code;
                    /** @var Contract[] $contract */
                    $contracts = (new Query())
                        ->select('*')
                        ->from(Contract::tableName().' as c, '.ContractorContract::tableName().' as cc')
                        ->where('c.id = cc.contract_id')
                        ->andWhere(['c.contract_type_id' => $contractTypeId, 'cc.parent_id' => $contractor->id])
                        ->all();
                    /*Contract::find()
                    ->select('*')
                    ->alias('c')
                    ->leftJoin(ContractorContract::tableName().' AS cc ON c.id = cc.contract_id')
                    ->andWhere(['c.contract_type_id' => $contractTypeId, 'cc.parent_id' => $contractorIds])
                    ->all();*/
                    if ($contracts) {
                        foreach ($contracts as $contract) {
                            $contract_code = $contract['contract_code'].": ".$contract['address'];
                            if (!isset($requestData['contractsByContractors'][$contract['parent_id']])) {
                                $requestData['contractsByContractors'][$contract['parent_id']] = [];
                            }
                            $requestData['contractsByContractors'][$contract['parent_id']][] = $contract;
                            $model->contract_code[$contract['contract_id']] = $contract_code;
                            $logic[$contractor->id][$contract['contract_id']] = $contract_code;
                            //$model->address[] = $contract['address'];
                        }
                    }
                }
            }
        }
        $model->logic = $logic;
        if ($model->load($requestData) && $model->validate()) {
            $model->proceed();
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
