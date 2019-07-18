<?php

namespace backend\actions\reference\file;

use backend\actions\BackendModelAction;
use backend\models\form\UploadFileForm;
use common\models\reference\File;
use yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Действие для вывода формы редактирования существующего файла
 */
class UpdateAction extends BackendModelAction
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function run($id)
    {
        /** @var File $file */
        $file = $this->controller->findModel($id);
        $model = new UploadFileForm();
        $model->populate($file);
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
