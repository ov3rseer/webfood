<?php

namespace backend\models\system;

use common\models\enum\ContractType;
use common\models\enum\ServiceObjectType;
use common\models\form\SystemForm;
use backend\widgets\ActiveField;
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
 * Форма для импорта объектов обслуживания и договоров
 *
 * @property File $file
 * @property ServiceObjectType $serviceObjectType
 * @property ContractType $contractType
 * @property UploadedFile[] $uploadedFiles
 * @property integer $file_id
 * @property integer $service_object_type_id
 * @property integer $contract_type_id
 */
class ImportServiceObjectAndContractForm extends SystemForm
{
    /**
     * @var UploadedFile[] загружаемый файл
     */
    public $uploadedFiles;

    /**
     * @var integer id загруженного файла
     */
    public $file_id;

    /**
     * @var integer id тип объекта обслуживания
     */
    public $service_object_type_id;

    /**
     * @var integer id типа договора
     */
    public $contract_type_id;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Импорт объектов обслуживания и договоров';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['uploadedFiles'], 'file', 'maxFiles' => 20, 'extensions' => 'xml', 'when' => function () {
                if (!$this->file_id) {
                    return true;
                }
                return false;
            }],
            [['file_id'], 'integer'],
            [['contract_type_id', 'service_object_type_id'], 'integer'],
            [['contract_type_id', 'service_object_type_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'uploadedFiles' => 'Файлы для загрузки',
            'contract_type_id' => 'Тип договора',
            'service_object_type_id' => 'Тип объекта обслуживания',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['file_id']['displayType'] = ActiveField::HIDDEN;
            $this->_fieldsOptions['uploadedFiles']['displayType'] = ActiveField::FILE;
        }
        return $this->_fieldsOptions;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFile()
    {
        return File::find()->andWhere(['id' => $this->file_id]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getContractType()
    {
        return ContractType::find()->andWhere(['id' => $this->contract_type_id]);
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
        $files_id = [];
        $this->uploadedFiles = UploadedFile::getInstances($this, 'uploadedFiles');
        foreach ($this->uploadedFiles as $uploadedFile) {
            if (!$uploadedFile->error) {
                $file = new File();
                $file->setUploadFile($uploadedFile);
                $file->path = 'service-object-and-contract';
                $file->save();
                $files_id[] = $file->id;
            }
        }
        $consoleTaskType = ConsoleTaskType::findOne(ConsoleTaskType::IMPORT_SERVICE_OBJECT_AND_CONTRACT);
        if (!empty($files_id) && $consoleTaskType) {
            $consoleTask = new ConsoleTask();
            $consoleTask->type_id = $consoleTaskType->id;
            $consoleTask->is_repeatable = false;
            $consoleTask->name = (string)$consoleTaskType;
            $consoleTask->status_id = ConsoleTaskStatus::PLANNED;
            $consoleTask->params = Json::encode([
                'files_id' => $files_id,
                'contract_type_id' => $this->contract_type_id,
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