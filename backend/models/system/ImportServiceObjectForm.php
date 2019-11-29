<?php

namespace backend\models\system;

use common\models\enum\ServiceObjectType;
use common\components\DateTime;
use common\models\enum\ConsoleTaskStatus;
use common\models\enum\ConsoleTaskType;
use common\models\reference\ConsoleTask;
use common\models\reference\File;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Форма для импорта объектов обслуживания
 *
 * @property integer $file_id
 * @property integer $service_object_type_id
 *
 * @property File $file
 * @property ServiceObjectType $serviceObjectType
 * @property UploadedFile $uploadedFile
 */
class ImportServiceObjectForm extends ImportForm
{
    /**
     * @var integer id тип объекта обслуживания
     */
    public $service_object_type_id;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Импорт объектов обслуживания';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_type_id'], 'integer'],
            [['service_object_type_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object_type_id' => 'Тип объекта обслуживания',
        ]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getServiceObjectType()
    {
        return ServiceObjectType::find()->andWhere(['id' => $this->service_object_type_id]);
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function proceed()
    {
        parent::proceed();
        $consoleTaskType = ConsoleTaskType::findOne(ConsoleTaskType::IMPORT_SERVICE_OBJECT);
        if ($this->file_id && $consoleTaskType) {
            $consoleTask = new ConsoleTask();
            $consoleTask->type_id = $consoleTaskType->id;
            $consoleTask->is_repeatable = false;
            $consoleTask->name = (string)$consoleTaskType;
            $consoleTask->status_id = ConsoleTaskStatus::PLANNED;
            $consoleTask->params = Json::encode([
                'file_id' => $this->file_id,
                'service_object_type_id' => $this->service_object_type_id,
            ]);
            $consoleTask->start_date = new DateTime('now');
            $consoleTask->save();
            Yii::$app->session->setFlash('success',
                'Файл будет загружен в ближайшее время. Статус загрузки можно просмотреть в отчете ' . Html::a('Задачи', ['/report/tasks'], ['target' => '_blank']) . '.'
            );
        }
    }
}