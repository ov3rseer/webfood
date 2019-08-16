<?php

namespace frontend\actions\form\serviceObject\uploadLists;

use frontend\actions\FrontendModelAction;
use frontend\models\serviceObject\UploadLists;
use Yii;
use yii\base\UserException;

/**
 * Действие для вывода формы
 */
class IndexAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @throws UserException
     */
    public function run()
    {
        /** @var UploadLists $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isPost) {
            $model->load($requestData);
            $action = Yii::$app->request->post('action');
            if ($action == UploadLists::SCENARIO_HAND_INPUT) {
                $model->scenario = $action;
                if ($model->load($requestData) && $model->validate()) {
                    $model->submit();
                    $this->controller->refresh();
                }
            }
            if ($action == UploadLists::SCENARIO_UPLOAD_FILE) {
                $model->scenario = $action;
                if ($model->load($requestData) && $model->validate()) {
                    $model->proceed();
                    $this->controller->refresh();
                }
            }
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
