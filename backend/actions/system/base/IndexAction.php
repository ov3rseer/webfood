<?php

namespace backend\actions\system\base;

use backend\actions\ModelAction;
use backend\models\form\SystemForm;
use backend\widgets\ActiveForm;
use Yii;
use yii\web\Response;

/**
 * Действие для вывода формы
 */
class IndexAction extends ModelAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        /** @var SystemForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load($requestData) && $model->validate()) {
            $model->proceed();
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
