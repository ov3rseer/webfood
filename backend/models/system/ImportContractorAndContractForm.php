<?php

namespace backend\models\system;

use backend\models\form\SystemForm;
use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\enum\ConsoleTaskStatus;
use common\models\enum\ConsoleTaskType;
use common\models\reference\ConsoleTask;
use common\models\reference\File;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Форма для импорта контрагентов и договоров
 *
 * @property File           $file
 * @property UploadedFile[] $uploadedFiles
 * @property integer        $file_id
 */
class ImportContractorAndContractForm extends SystemForm
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
     * @inheritdoc
     */
    public function getName()
    {
        return 'Импорт контрагентов и контрактов';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['uploadedFiles'], 'file', 'maxFiles' => 0, 'extensions' => 'xml', 'when' => function () {
                if (!$this->file_id) {
                    return true;
                }
                return false;
            }],
            [['file_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'uploadedFiles' => 'Файлы для загрузки',
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
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getFile()
    {
        return File::find()->andWhere(['id' => $this->file_id]);
    }

    /**
     * @inheritdoc
     * @throws \yii\base\UserException
     */
    public function proceed()
    {
        $files_id = [];
        $this->uploadedFiles = UploadedFile::getInstances($this, 'uploadedFiles');
        foreach ($this->uploadedFiles as $uploadedFile) {
            if (!$uploadedFile->error) {
                $file = new File();
                $file->setUploadFile($uploadedFile);
                $file->path = 'contractor-and-contract';
                $file->save();
                $files_id[] = $file->id;
            }
        }
        $consoleTaskType = ConsoleTaskType::findOne(ConsoleTaskType::IMPORT_CONTRACTOR_AND_CONTRACT);
        if (!empty($files_id) && $consoleTaskType) {
            $consoleTask = new ConsoleTask();
            $consoleTask->type_id = $consoleTaskType->id;
            $consoleTask->is_repeatable = false;
            $consoleTask->name = (string)$consoleTaskType;
            $consoleTask->status_id = ConsoleTaskStatus::PLANNED;
            $consoleTask->params = Json::encode([
                'files_id' => $files_id,
            ]);
            $consoleTask->start_date = new DateTime('now');
            $consoleTask->save();
            Yii::$app->session->setFlash('success',
                'Файл будет загружен в ближайшее время. Статус загрузки можно просмотреть в отчете ' . Html::a('Задачи', ['/report/tasks'], ['target' => '_blank']) . '.'
            );
        }
    }
}