<?php

namespace backend\controllers\report;

use common\helpers\ArrayHelper;
use common\models\reference\ConsoleTask;
use yii\web\NotFoundHttpException;

/**
 * Контроллер отчета "Задачи"
 */
class TasksController extends ReportController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'backend\models\report\Tasks';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['status'],
                        'allow'   => true,
                        'roles'   => ['super-admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Просмотр статуса задачи
     * @param integer $id идентификатор задачи
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStatus($id)
    {
        $model = ConsoleTask::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Задача с указанным ID не найдена: ' . $id);
        }
        return $this->renderUniversal('@backend/views/report/tasks/status', ['model' => $model]);
    }
}
