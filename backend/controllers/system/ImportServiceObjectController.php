<?php

namespace backend\controllers\system;

use common\helpers\ArrayHelper;
use Yii;
use yii\filters\AccessControl;

/**
 * Контроллер для управления импортом объектов обслуживания
 */
class ImportServiceObjectController extends SystemController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'backend\models\system\ImportServiceObjectForm';

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
                        'actions' => ['download-example-file'],
                        'allow'   => true,
                        'roles'   => ['super-admin'],
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
                'viewPath' => '@backend/views/system/import/service-object',
            ],
        ]);
    }

    /**
     * Скачивание файла-образца
     */
    public function actionDownloadExampleFile()
    {
        return Yii::$app->response->sendFile(
            Yii::getAlias('@backend/web/samples/import/service-objects.xml'),
            'Файл-образец с объектами обслуживания.xml'
        );
    }
}