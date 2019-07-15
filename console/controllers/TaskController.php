<?php

namespace console\controllers;

use common\components\DateTime;
use common\models\enum\ConsoleTaskStatus;
use common\models\reference\ConsoleTask;
use Yii;
use yii\console\Controller;
use yii\console\Exception;

/**
 * Запуск задач на выполнение
 */
class TaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $parentResult = parent::beforeAction($action);
        if ($parentResult) {
            Yii::setAlias('@webroot', '@backend/web');
        }
        return $parentResult;
    }

    /**
     * Проверка и запуск запланированных задач
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
     */
    public function actionIndex()
    {
        $curDate = new DateTime();

        // Обработка зависших задач

        /** @var ConsoleTask[] $tasksInProgress */
        $tasksInProgress = ConsoleTask::find()
            ->active()
            ->andWhere(['status_id' => ConsoleTaskStatus::IN_PROGRESS])
            ->all();
        foreach ($tasksInProgress as $task) {
            if (!$task->isRunning()) {
                $task->result_text = 'Задача прервана из-за ошибки сервера';
                if ($task->is_repeatable && $task->repeat_interval) {
                    $task->status_id = ConsoleTaskStatus::PLANNED;
                    $task->setNewStartDate();
                } else {
                    $task->status_id = ConsoleTaskStatus::INTERRUPTED;
                }
                $task->save();
            }
        }

        // Запуск запланированных задач

        /** @var ConsoleTask[] $tasksToRun */
        $tasksToRun = ConsoleTask::find()
            ->active()
            ->andWhere(
                'start_date <= :curr_min_end',
                [':curr_min_end' => $curDate->format('Y-m-d H:i:59')]
            )
            ->andWhere(['status_id' => ConsoleTaskStatus::PLANNED])
            ->all();
        foreach ($tasksToRun as $task) {
            if (!$task->isRunning()) {
                $task->executeAsync();
            }
        }
    }

    /**
     * Запуск запланированной задачи
     * @param integer $consoleTaskId
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionExec($consoleTaskId)
    {
        /** @var ConsoleTask $consoleTask */
        $consoleTask = ConsoleTask::find()
            ->active()
            ->andWhere([
                'id' => (integer)$consoleTaskId,
                'status_id' => ConsoleTaskStatus::PLANNED,
            ])
            ->one();
        if (!$consoleTask) {
            throw new Exception('Указанная задача не найдена или не подходит по статусу: ' . $consoleTaskId);
        }
        $consoleTask->execute();
    }
}
