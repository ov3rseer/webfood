<?php

namespace backend\models\form;

use backend\widgets\ActiveField;
use common\models\form\Form;
use common\models\reference\File;
use yii\web\UploadedFile;

/**
 * Форма для загрузки произвольного файла
 *
 * @property string         $comment
 * @property string         $name_full
 * @property string         $file_id
 * @property UploadedFile   $uploadedFile
 *
 * Отношения:
 * @property File $file
 */
class UploadFileForm extends Form
{
    /**
     * @var UploadedFile загружаемое изображение
     */
    public $uploadedFile;

    /**
     * @var integer идентификатор модели файла
     */
    public $file_id;

    /**
     * @var string заголовок файла
     */
    public $name_full;

    /**
     * @var string комментарий к файлу
     */
    public $comment;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['uploadedFile'], 'file'],
            [['name_full', 'comment'], 'string'],
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
            'file_id'      => 'Файл',
            'name_full'    => 'Заголовок',
            'comment'      => 'Комментарий',
        ]);
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
     * Наполнение модели формы на основании модели файла
     * @param File $file
     */
    public function populate($file)
    {
        $this->file_id = $file->id;
        $this->name_full = $file->name_full;
        $this->comment = $file->comment;
    }

    /**
     * Подтверждение формы
     * @throws \yii\base\UserException
     */
    public function submit()
    {
        $file = $this->file_id ? $this->file : new File();
        $file->setUploadFile($this->uploadedFile);
        $file->path = 'upload';
        $file->name_full = $this->name_full;
        $file->comment = $this->comment ? $this->comment : '';
        $file->save();
    }
}
