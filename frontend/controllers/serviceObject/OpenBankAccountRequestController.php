<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use Yii;
use yii\filters\AccessControl;

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
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\serviceObject\openBankAccount\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/request/open-bank-account-request/index',
            ],
        ]);
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