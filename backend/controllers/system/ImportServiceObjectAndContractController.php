<?php

namespace backend\controllers\system;

use common\helpers\ArrayHelper;
use Yii;
use yii\filters\AccessControl;

/**
 * Контроллер для управления импортом объектов обслуживания и договоров
 */
class ImportServiceObjectAndContractController extends SystemController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'backend\models\system\ImportServiceObjectAndContractForm';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['download-example-file'],
                        'allow'   => true,
                        'roles'   => [static::className() . '.Index'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\system\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/system/import-service-object-and-contract/index',
            ],
        ]);
    }

    /**
     * Скачивание файла-образца
     */
    public function actionDownloadExampleFile()
    {
        return Yii::$app->response->sendFile(
            Yii::getAlias('@backend/web/samples/import-service-object-and-contract/service-object-and-contract.xml'),
            'Файл-образец с объектами обслуживания и контрактами.xml'
        );
    }
}