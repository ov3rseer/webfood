<?php

namespace frontend\actions\form\requesttable;

use common\models\reference\Contract;
use common\models\tablepart\ContractProduct;
use frontend\actions\FrontendModelAction;
use backend\widgets\ActiveForm;
use frontend\models\request\RequestTableForm;
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
        /** @var RequestTableForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $userId = Yii::$app->user->id;
        $contractorName = Yii::$app->request->get('contractorName');
        $contractId = Yii::$app->request->get('contractCode');
        if ($userId) {
            $contractProductByDates = (new Query())
                ->select('*')
                ->from(ContractProduct::tableName().', ')
                ->andWhere([ContractProduct::tableName().'.parent_id' => $contractId])
                ->andWhere([ContractProduct::tableName().'.parent_id' => 'tab_request_date.parent_id'])
                ->all();
            var_dump($contractProductByDates);
        }
        if ($model->load($requestData) && $model->validate()) {
            $model->proceed();
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
