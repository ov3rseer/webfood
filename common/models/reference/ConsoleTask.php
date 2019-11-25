<?php

namespace common\models\reference;

use common\components\DateTime;
use common\components\DbManager;
use common\components\TaskProcessorInterface;
use common\models\enum\ConsoleTaskStatus;
use common\models\enum\ConsoleTaskType;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\db\Connection;
use yii\helpers\Json;
use yii\log\Logger;

/**
 * Модель задачи, запускаемой cron'ом
 *
 * Свойства:
 * @property integer  $type_id         тип задачи
 * @property integer  $status_id       статус задачи
 * @property string   $params          параметры задачи (json)
 * @property DateTime $start_date      время ближайшего запуска
 * @property DateTime $finish_date     дата последнего завершения
 * @property boolean  $is_repeatable   флаг повторяемой задачи
 * @property string   $repeat_interval период повторения
 * @property string   $result_text     результат (отображаемый)
 * @property string   $result_data     данные результата (не отображаемые) (json)
 *
 * Отношения:
 * @property ConsoleTaskType   $type
 * @property ConsoleTaskStatus $status
 */
class ConsoleTask extends Reference
{
    /**
     * @var  Connection
     */
    private static $_db;

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        if (is_null(self::$_db)) {
            self::$_db = parent::getDb();
        }
        return self::$_db;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type_id', 'start_date', 'status_id'], 'required'],
            [['start_date'], 'filter', 'skipOnEmpty' => true, 'filter' => function($value) {
                return (new DateTime($value))->format(DateTime::DB_DATETIME_FORMAT);
            }],
            [['start_date', 'finish_date'], 'date', 'format' => 'php:' . DateTime::DB_DATETIME_FORMAT],
            [['is_repeatable'], 'default', 'value' => false],
            [['is_repeatable'], 'boolean'],
            [['repeat_interval'], 'string'],
            [['repeat_interval'], 'required', 'when' => function($model) { return $model->is_repeatable; }],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        foreach ($result[self::SCENARIO_DEFAULT] as $key => $value) {
            if ($value == 'repeat_interval') {
                unset($result[self::SCENARIO_DEFAULT][$key]);
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type_id'         => 'Тип задачи',
            'is_repeatable'   => 'Повторяемая',
            'start_date'      => 'Дата начала',
            'finish_date'     => 'Дата завершения',
            'repeat_interval' => 'Интервал повторения',
            'result_text'     => 'Результат',
            'status_id'       => 'Статус',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ConsoleTaskType::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(ConsoleTaskStatus::className(), ['id' => 'status_id']);
    }

    /**
     * Установка новой даты запуска (для повторяющихся задач)
     * @throws \Exception
     */
    public function setNewStartDate()
    {
        if ($this->is_repeatable && $this->repeat_interval) {
            $this->start_date = $this->getNewStartDate();
        }
    }

    /**
     * Получение новой даты выполнения задачи
     * @param DateTime $curDate
     * @return DateTime|null
     * @throws \Exception
     */
    public function getNewStartDate($curDate = null)
    {
        $repeatInterval = Json::decode($this->repeat_interval);
        if (is_null($curDate)) {
            $curDate = new DateTime();
        }
        if (empty($repeatInterval['monthOfYear'])) {
            $repeatInterval['monthOfYear'] = range(1, 12);
        } else {
            sort($repeatInterval['monthOfYear']);
        }
        if (empty($repeatInterval['dayOfMonth'])) {
            $repeatInterval['dayOfMonth'] = range(1, 31);
        } else {
            sort($repeatInterval['dayOfMonth']);
        }
        if (empty($repeatInterval['dayOfWeek'])) {
            $repeatInterval['dayOfWeek'] = range(1, 7);
        }
        if (!isset($repeatInterval['hourOfDay']) || $repeatInterval['hourOfDay'] === '' || $repeatInterval['hourOfDay'] === null || $repeatInterval['hourOfDay'] === []) {
            $repeatInterval['hourOfDay'] = range(0, 23);
        } else {
            sort($repeatInterval['hourOfDay']);
        }
        if (!isset($repeatInterval['minuteOfHour']) || $repeatInterval['minuteOfHour'] === '' || $repeatInterval['minuteOfHour'] === null || $repeatInterval['minuteOfHour'] === []) {
            $repeatInterval['minuteOfHour'] = range(0, 59);
        } else {
            sort($repeatInterval['minuteOfHour']);
        }
        $curYear = (integer)$curDate->format('Y');
        $years = [$curYear, $curYear + 1];
        foreach ($years as $year) {
            foreach ($repeatInterval['monthOfYear'] as $month) {
                foreach ($repeatInterval['dayOfMonth'] as $day) {
                    $newDateTime = new DateTime();
                    $newDateTime->setDate($year, $month, $day);
                    if ($day != $newDateTime->format('j')) {
                        continue;
                    }
                    if (!in_array($newDateTime->format('N'), $repeatInterval['dayOfWeek'])) {
                        continue;
                    }
                    foreach ($repeatInterval['hourOfDay'] as $hour) {
                        foreach ($repeatInterval['minuteOfHour'] as $minute) {
                            $newDateTime->setTime($hour, $minute);
                            if ((string)$newDateTime > (string)$curDate) {
                                return $newDateTime;
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * Хелпер получения структуры интервалов на основании интервала в минутах
     *
     * @param integer $minutes
     * @return array
     */
    public static function getIntervalFromMinutes($minutes)
    {
        if ($minutes > 30) {
            if ($minutes > 720) {
                $repeatInterval = [
                    'dayOfMonth' => range(0, 31, min(31, round($minutes / 60 / 24))),
                    'hourOfDay' => [0],
                    'minuteOfHour' => [0],
                ];
            } else {
                $repeatInterval = [
                    'hourOfDay' => range(0, 23, round($minutes / 60)),
                    'minuteOfHour' => [0],
                ];
            }
        } else {
            $repeatInterval = [
                'minuteOfHour' => range(0, 59, max(1, $minutes))
            ];
        }
        return $repeatInterval;
    }

    /**
     * Выполнение задачи в фоновом режиме
     */
    public function executeAsync()
    {
        exec('php ' . Yii::getAlias('@root/yii') . ' task/exec ' . $this->id . ' > /dev/null 2>&1 &');
    }

    /**
     * Выполнение задачи
     * @throws InvalidConfigException
     * @throws UserException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function execute()
    {
        $taskProcessorClass = ConsoleTaskType::getTaskProcessorClassByTypeId($this->type_id);
        if (!$taskProcessorClass) {
            throw new InvalidConfigException('Для данного типа задач не указан класс обработчика');
        }
        $this->status_id = ConsoleTaskStatus::IN_PROGRESS;
        $this->save();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var TaskProcessorInterface $taskProcessor */
            $taskProcessor = new $taskProcessorClass();
            $result = $taskProcessor->processTask($this);
            if ($this->is_repeatable && $this->repeat_interval) {
                $this->setNewStartDate();
                $this->status_id = ConsoleTaskStatus::PLANNED;
            } else {
                $this->status_id = ConsoleTaskStatus::FINISHED;
            }
            $this->result_text = $result['result_text'];
            $this->result_data = Json::encode($result['result_data']);
            $this->finish_date = new DateTime();
            $this->save();
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            if ($ex instanceof UserException) {
                $this->result_text = $ex->getMessage();
            } else {
                $errorNumber = Yii::$app->security->generateRandomString(6);
                $this->result_text = 'Ошибка сервера. Код ошибки: ' . $errorNumber;
                Yii::$app->log->logger->log('Код ошибки: ' . $errorNumber . "\n" . (string)$ex, Logger::LEVEL_ERROR, 'application.exception');
            }
            $this->result_data = Json::encode(['exception' => (string)$ex]);
            $this->finish_date = new DateTime();
            if ($this->is_repeatable && $this->repeat_interval) {
                $this->setNewStartDate();
                $this->status_id = ConsoleTaskStatus::PLANNED;
            } else {
                $this->status_id = ConsoleTaskStatus::INTERRUPTED;
            }
            $this->save();
        }
    }

    /**
     * Получение ID выполняющегося процесса задачи
     * @return integer|false
     */
    public function getProcessId()
    {
        $result = [];
        $command =
            'ps aux --sort=-pid ' .
            ' | grep -v grep ' .
            ' | grep \'php ' . Yii::getAlias('@root/yii') . ' task/exec ' . $this->id . '\'' .
            ' | awk \'{print $2}\'';
        exec($command, $result);
        return count($result) > 0 ? $result[0] : false;
    }

    /**
     * Получение статуса выполнения задачи в системе
     * @return boolean
     */
    public function isRunning()
    {
        return !$this->isNewRecord && (boolean)$this->getProcessId();
    }

    /**
     * Прерывание процесса задачи
     */
    public function kill()
    {
        if ($pid = $this->getProcessId()) {
            exec('kill -9 ' . $pid);
        }
    }

    /**
     * Обновление статуса (в отдельной транзакции для фиксации прогресса длительных задач)
     * @param string $text
     * @param array $data
     * @throws \yii\db\Exception
     * @throws UserException
     */
    public function updateResult($text, $data = [])
    {
        $tmpConnection = clone self::getDb();
        $tmpConnection->open();
        self::$_db = $tmpConnection;
        $this->result_text = $text;
        $this->result_data = Json::encode($data);
        $this->save();
        $tmpConnection->close();
        self::$_db = null;
    }

    /**
     * @inheritdoc
     */
    static public function find()
    {
        $result = parent::find();
        if (Yii::$app->id == 'app-backend' && !Yii::$app->user->can(DbManager::ADMIN_ROLE)) {
            $result->andWhere(['create_user_id' => Yii::$app->user->id]);
        }
        return $result;
    }
}
