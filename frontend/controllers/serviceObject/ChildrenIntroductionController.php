<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use frontend\models\serviceObject\ChildrenIntroductionForm;
use frontend\models\serviceObject\ChildrenIntroductionUploadFile;
use PhpOffice\PhpSpreadsheet\Exception as SpreedsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * Контроллер для формы "Загрузка списков"
 */
class ChildrenIntroductionController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\ChildrenIntroductionForm';

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
     * @throws SpreedsheetException
     * @throws Exception
     */
    public function actionIndex()
    {
        /** @var ChildrenIntroductionForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $action = Yii::$app->request->post('action');
        $model->load($requestData);
        if ($action == ChildrenIntroductionForm::SCENARIO_HAND_INPUT) {
            $model->scenario = $action;
            if ($model->validate()) {
                $model->proceed();
            }
        }
        $openCardUploadFile = new ChildrenIntroductionUploadFile();
        if ($action == ChildrenIntroductionForm::SCENARIO_UPLOAD_FILE) {
            $openCardUploadFile->uploadedFile = UploadedFile::getInstance($openCardUploadFile, 'uploadedFile');
            if ($openCardUploadFile->uploadedFile && !$openCardUploadFile->uploadedFile->error) {
                $model->scenario = $action;
                if ($openCardUploadFile->validate()) {
                    $openCardUploadFile->proceed();
                }
            }
        }
        $model->scenario = ChildrenIntroductionForm::SCENARIO_HAND_INPUT;
        return $this->renderUniversal('@frontend/views/service-object/children-introduction/index', ['model' => $model, 'uploadFileForm' => $openCardUploadFile]);
    }

    /**
     * Скачивание файла-образца
     */
    public function actionDownloadExampleFile()
    {
        return Yii::$app->response->sendFile(
            Yii::getAlias('@frontend/web/samples/children-introduction/children.xlsx'),
            'Файл-образец для загрузки в систему учащихся.xlsx'
        );
    }
}