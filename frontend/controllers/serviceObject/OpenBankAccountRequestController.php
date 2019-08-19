<?php

namespace frontend\controllers\serviceObject;

use backend\widgets\ActiveForm;
use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use frontend\models\serviceObject\OpenBankAccountRequest;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Контроллер для формы "Загрузка списков"
 */
class OpenBankAccountRequestController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\OpenBankAccountRequest';

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
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                    [
                        'actions' => ['download-example-file'],
                        'allow'   => true,
                        'roles'   => ['service-object'],
                    ],
                ],
            ]
        ]);
    }

    /**
     * @return array|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws Exception
     * @throws UserException
     */
    public function actionIndex()
    {
        /** @var OpenBankAccountRequest $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $model->load($requestData);
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $model->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('action');
            if ($action == OpenBankAccountRequest::SCENARIO_HAND_INPUT) {
                $model->scenario = $action;
                if ($model->validate()) {
                    $model->submit();
                }
            }
            if ($action == OpenBankAccountRequest::SCENARIO_UPLOAD_FILE) {
                $model->scenario = $action;
                if ($model->validate()) {
                    $model->proceed();
                }
            }
        }
        return $this->renderUniversal('@frontend/views/service-object/request/open-bank-account-request/index', ['model' => $model]);
    }

    /**
     * Скачивание файла-образца
     */
    public function actionDownloadExampleFile()
    {
        return Yii::$app->response->sendFile(
            Yii::getAlias('@frontend/web/samples/open-bank-account-request/open-bank-account.xlsx'),
            'Файл-образец для загрузки в систему учащихся и открытия счетов.xlsx'
        );
    }
}