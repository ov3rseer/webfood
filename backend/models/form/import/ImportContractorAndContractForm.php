<?php

namespace backend\models\form\import;

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
 * @property UploadedFile   $uploadedFile
 * @property integer        $file_id
 */
class ImportContractorAndContractForm extends SystemForm
{
    /**
     * @var UploadedFile загружаемый файл
     */
    public $uploadedFile;

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
            [['uploadedFile'], 'file', 'extensions' => 'xml', 'when' => function () {
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
            'uploadedFile' => 'Файл для загрузки',
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
            $this->_fieldsOptions['uploadedFile']['displayType'] = ActiveField::FILE;
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
        if ($this->uploadedFile && !$this->uploadedFile->error) {
            $file = new File();
            $file->setUploadFile($this->uploadedFile);
            $file->path = 'articles_numbers';
            $file->save();
            $consoleTaskType = ConsoleTaskType::findOne(ConsoleTaskType::IMPORT_CONTRACTOR_AND_CONTRACT);
            if ($consoleTaskType) {
                $consoleTask = new ConsoleTask();
                $consoleTask->type_id = $consoleTaskType->id;
                $consoleTask->is_repeatable = false;
                $consoleTask->name = (string)$consoleTaskType;
                $consoleTask->status_id = ConsoleTaskStatus::PLANNED;
                $consoleTask->params = Json::encode([
                    'file_id' => $file->id,
                ]);
                $consoleTask->start_date = new DateTime('now +1 min');
                $consoleTask->save();
                Yii::$app->session->setFlash('success',
                    'Файл будет загружен в ближайшее время. Статус загрузки можно просмотреть в отчете ' . Html::a('Задачи', ['/report/tasks'], ['target' => '_blank']) . '.'
                );
            }
        }
    }
}