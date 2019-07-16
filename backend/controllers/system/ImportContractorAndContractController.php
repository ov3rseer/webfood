<?php

namespace backend\controllers\system;

use backend\models\form\import\ImportContractorAndContractForm;
use backend\widgets\ActiveForm;
use Yii;
use yii\helpers\Html;
use yii\web\Response;

/**
 * Контроллер для управления импортом контрагентов и договоров
 */
class ImportContractorAndContractController extends SystemController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'backend\models\form\import\ImportContractorAndContractForm';

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
     * @return array|string|\yii\console\Response|Response
     * @throws \yii\base\UserException
     */
    public function actionIndex()
    {
        /** @var ImportContractorAndContractForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $model->load($requestData);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->validate()) {
            if ($model->file_id) {
                $model->proceed();
                Yii::$app->session->setFlash('success',
                    'Файл будет загружен в ближайшее время. Статус загрузки можно просмотреть в отчете ' . Html::a('Задачи', ['/report/tasks'], ['target' => '_blank']) . '.'
                );
                return Yii::$app->response->redirect('index');
            }
        }
        if ($model->uploadedFile && !$model->file_id) {
            $model->uploadFile();
        }
        return $this->renderUniversal('@backend/views/system/upload-contractor-and-contract/index', ['model' => $model]);
    }
}