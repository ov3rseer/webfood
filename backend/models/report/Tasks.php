<?php

namespace backend\models\report;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\enum\ConsoleTaskStatus;
use common\models\enum\ConsoleTaskType;
use common\models\reference\ConsoleTask;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * Отчет "Задачи"
 *
 * Отношения:
 * @property ConsoleTaskType $taskType
 * @property ConsoleTaskStatus $taskStatus
 */
class Tasks extends Report
{
    /**
     * @var string
     */
    public $date_interval;

    /**
     * @var string
     */
    public $task_name;

    /**
     * @var integer
     */
    public $task_type_id;

    /**
     * @var integer
     */
    public $task_status_id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->date_interval =
            (new DateTime('-7 days'))->format('Y-m-d 00:00:00') . ' - ' .
            (new DateTime())->format('Y-m-d 23:59:59');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['task_type_id', 'task_status_id'], 'integer'],
            [['date_interval'], 'string'],
            [['task_name'], 'string'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date_interval'  => 'Дата',
            'task_name'      => 'Наименование',
            'task_type_id'   => 'Тип',
            'task_status_id' => 'Статус',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['date_interval']['displayType'] = ActiveField::DATETIME;
        }
        return $this->_fieldsOptions;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTaskType()
    {
        return ConsoleTaskType::find()->andWhere(['id' => $this->task_type_id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTaskStatus()
    {
        return ConsoleTaskStatus::find()->andWhere(['id' => $this->task_status_id]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Задачи';
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function getDataProvider()
    {
        $query = ConsoleTask::find();
        if (mb_strpos($this->date_interval, ' - ') !== false) {
            $interval = explode(' - ', $this->date_interval);
            if (count($interval) == 2) {
                $query->andWhere(['BETWEEN', 'start_date', $interval[0], $interval[1]]);
            }
        }
        if ($this->task_name) {
            $query->andWhere(['LIKE', 'LOWER(name)', mb_strtolower($this->task_name)]);
        }
        if ($this->task_type_id) {
            $query->andWhere(['type_id' => $this->task_type_id]);
        }
        if ($this->task_status_id) {
            $query->andWhere(['status_id' => $this->task_status_id]);
        }
        if (!$query->where) {
            $query->andWhere('1=0');
        }
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 100,
                'pageSizeLimit' => false,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [
            'id',
            [
                'attribute' => 'start_date',
                'headerOptions' => ['style' => 'width:160px;'],
            ],
            'name',
            'type',
            'status',
            [
                'attribute' => 'finish_date',
                'headerOptions' => ['style' => 'width:160px;'],
            ],
            [
                'attribute' => 'result_text',
                'headerOptions' => ['style' => 'width:160px;'],
                'format' => 'raw',
                'value' => function($rowModel) {
                    /** @var ConsoleTask $rowModel */
                    return Html::a('Показать', ['status', 'id' => $rowModel->id], ['target' => '_blank', 'data-pjax' => 0]);
                },
            ],
        ];
    }
}
