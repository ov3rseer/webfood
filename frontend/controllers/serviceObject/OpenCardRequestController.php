<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use frontend\models\serviceObject\openCard\OpenCardRequest;
use frontend\models\serviceObject\openCard\OpenCardUploadFile;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * Контроллер для формы "Загрузка списков"
 */
class OpenCardRequestController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\openCard\OpenCardRequest';

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
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ]
        ]);
    }

    /**
     * @return array|string
     * @throws UserException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function actionIndex()
    {
        /** @var OpenCardRequest $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $action = Yii::$app->request->post('action');
        $model->load($requestData);
        if ($action == OpenCardRequest::SCENARIO_HAND_INPUT) {
            $model->scenario = $action;
            if ($model->validate()) {
                $model->proceed();
            }
        }
        $openCardUploadFile = new OpenCardUploadFile();
        if (Yii::$app->request->post('OpenCardUploadFile') && $action == OpenCardRequest::SCENARIO_UPLOAD_FILE) {
            $openCardUploadFile->uploadedFile = UploadedFile::getInstance($openCardUploadFile, 'uploadedFile');
            $model->scenario = $action;
            if ($openCardUploadFile->validate()) {
                $openCardUploadFile->proceed();
            }
        }
        $model->scenario = OpenCardRequest::SCENARIO_HAND_INPUT;
        return $this->renderUniversal('@frontend/views/service-object/request/open-card-request/index', ['model' => $model, 'uploadFileForm' => $openCardUploadFile]);
    }

    /**
     * Скачивание файла-образца
     */
    public function actionDownloadExampleFile()
    {
        return Yii::$app->response->sendFile(
            Yii::getAlias('@frontend/web/samples/open-card-request/open-card.xlsx'),
            'Файл-образец для загрузки в систему учащихся и открытия счетов.xlsx'
        );
    }
}