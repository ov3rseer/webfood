<?php

namespace frontend\models\serviceObject\openCard;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\document\OpenCard;
use common\models\enum\DocumentStatus;
use common\models\enum\UserType;
use common\models\reference\Child;
use common\models\reference\File;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use common\models\tablepart\OpenCardChild;
use frontend\models\SystemForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\base\UserException;
use yii\web\UploadedFile;

class OpenCardUploadFileForm extends SystemForm
{
    /**
     * @var UploadedFile файл для загрузки
     */
    public $uploadedFile;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Загрузка файла';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uploadedFile'], 'file', 'extensions' => 'xls, xlsx, csv', 'checkExtensionByMimeType' => false,],
        ];
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
            $this->_fieldsOptions['uploadedFile']['displayType'] = ActiveField::FILE;
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     * @throws UserException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function proceed()
    {
        if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT && $this->uploadedFile && !$this->uploadedFile->error) {
            $file = new File();
            $file->setUploadFile($this->uploadedFile);
            $file->path = 'upload-lists';
            $file->save();
            if (!$file) {
                throw new UserException('Указанный файл не обнаружен');
            }

            /**  Identify the type of $inputFileName  **/
            $inputFileType = IOFactory::identify($file->getOriginalPath());
            /**  Create a new Reader of the type that has been identified  **/
            $reader = IOFactory::createReader($inputFileType);
            /**  Load $inputFileName to a Spreadsheet Object  **/
            $spreadsheet = $reader->load($file->getOriginalPath());
            $data = $spreadsheet->getActiveSheet()->getCellCollection();
            $cells = [];
            for ($row = 2; $row <= $data->getHighestRow(); $row++) {
                for ($column = 'A'; $column <= 'G'; $column++) {
                    $cells[$row][] = $data->get($column . $row)->getValue();
                }
            }

            $children = [];
            $serviceObject = ServiceObject::findOne(['user_id' => Yii::$app->user->id]);
            if ($serviceObject) {
                foreach ($cells as $row => $column) {
                    $surname = trim($column[0]) ?? null;
                    $forename = trim($column[1]) ?? null;
                    $patronymic = trim($column[2]) ?? null;
                    $classNumber = trim($column[3]) ?? null;
                    $classLitter = trim($column[4]) ?? null;
                    $codeword = trim($column[5]) ?? null;
                    $snils = trim($column[6]) ?? null;

                    $schoolClass = SchoolClass::findOne(['number' => $classNumber, 'litter' => $classLitter, 'service_object_id' => $serviceObject->id]);
                    if (!$schoolClass) {
                        $schoolClass = new SchoolClass();
                        $schoolClass->number = $classNumber;
                        $schoolClass->litter = $classLitter;
                        $schoolClass->service_object_id = $serviceObject->id;
                        $schoolClass->save();
                    }

                    $child = Child::findOne([
                        'surname' => $surname,
                        'forename' => $forename,
                        'patronymic' => $patronymic,
                        'service_object_id' => $serviceObject->id,
                        'school_class_id' => $schoolClass->id
                    ]);

                    if (!$child) {
                        $child = new Child();
                        $child->surname = $surname;
                        $child->forename = $forename;
                        $child->patronymic = $patronymic;
                        $child->service_object_id = $serviceObject->id;
                        $child->school_class_id = $schoolClass->id;
                        $child->save();
                        $children[$child->id]['snils'] = $snils;
                        $children[$child->id]['codeword'] = $codeword;
                    }
                }
                if (!empty($children)) {
                    $openBankAccount = new OpenCard();
                    $openBankAccount->date = new DateTime('now');
                    $openBankAccount->status_id = DocumentStatus::DRAFT;
                    $openBankAccount->service_object_id = $serviceObject->id;
                    $openBankAccount->save();
                    foreach ($children as $childId => $child) {
                        $openBankAccountChild = new OpenCardChild();
                        $openBankAccountChild->child_id = $childId;
                        $openBankAccountChild->parent_id = $openBankAccount->id;
                        $openBankAccountChild->codeword = $child['codeword'];
                        $openBankAccountChild->snils = $child['snils'];
                        $openBankAccountChild->save();
                    }
                    Yii::$app->session->setFlash('success', 'Заявка успешно оформлена.');
                } else {
                    Yii::$app->session->setFlash('error', 'На всех детей уже оформлены заявки.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Вы не являетесь объектом обслуживания.');
            }
        }
    }
}