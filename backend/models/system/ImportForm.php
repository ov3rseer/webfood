<?php


namespace backend\models\system;

use backend\widgets\ActiveField;
use common\models\form\SystemForm;
use common\models\reference\File;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

/**
 * Базовая форма для импорта
 *
 * @property File $file
 * @property UploadedFile $uploadedFile
 * @property integer $file_id
 */
abstract class ImportForm extends SystemForm
{
    /**
     * @var string путь для загрузки файла
     */
    public $path = 'default';

    /**
     * @var UploadedFile[] загружаемый файл
     */
    public $uploadedFile;

    /**
     * @var integer id загруженного файла
     */
    public $file_id;

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
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFile()
    {
        return File::find()->andWhere(['id' => $this->file_id]);
    }

    /**
     * @throws UserException
     */
    public function setUploadFile()
    {
        $this->uploadedFile = UploadedFile::getInstance($this, 'uploadedFile');
        if ($this->uploadedFile && !$this->uploadedFile->error) {
            $file = new File();
            $file->setUploadFile($this->uploadedFile);
            $file->path = $this->path;
            $file->save();
            $this->file_id = $file->id;
        }
    }

    /**
     * @return mixed|void
     * @throws UserException
     */
    public function proceed()
    {
        return $this->setUploadFile();
    }
}