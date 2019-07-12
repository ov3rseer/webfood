<?php

namespace backend\actions\report\base;

use backend\actions\ModelAction;
use backend\models\report\Report;
use backend\widgets\ActiveForm;
use yii;
use yii\web\Response;

/**
 * Действие для вывода отчета
 */
class IndexAction extends ModelAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var Report $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $model->load($requestData);
        $model->validate();
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
