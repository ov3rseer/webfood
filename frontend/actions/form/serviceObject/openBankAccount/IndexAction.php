<?php

namespace frontend\actions\form\serviceObject\openBankAccount;

use frontend\actions\FrontendModelAction;
use frontend\models\serviceObject\OpenBankAccountRequest;
use Yii;
use yii\base\Exception;
use yii\base\UserException;

/**
 * Действие для вывода формы
 */
class IndexAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @return string
     * @throws Exception
     * @throws UserException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function run()
    {
        /** @var OpenBankAccountRequest $model */
        $model = new $this->modelClass();
        if (Yii::$app->request->isPost) {
            $requestData = array_merge(Yii::$app->request->post());
            $action = Yii::$app->request->post('action');
            if ($action == OpenBankAccountRequest::SCENARIO_HAND_INPUT) {
                $model->scenario = $action;
                if ($model->load($requestData) && $model->validate()) {
                    $model->submit();
                }
            }
            if ($action == OpenBankAccountRequest::SCENARIO_UPLOAD_FILE) {
                $model->scenario = $action;
                if ($model->load($requestData) && $model->validate()) {
                    $model->proceed();
                }
            }
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
