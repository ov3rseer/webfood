<?php

namespace backend\actions\reference\file;

use backend\actions\ModelAction;
use backend\models\form\UploadFileForm;
use yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Действие для вывода формы загрузки нового файла
 */
class CreateAction extends ModelAction
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function run()
    {
        $model = new UploadFileForm();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            return ActiveForm::validate($model);
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->submit();
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw $ex;
            }
            return $this->controller->autoRedirect(['index']);
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
