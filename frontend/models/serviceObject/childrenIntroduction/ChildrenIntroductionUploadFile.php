<?php

namespace frontend\models\serviceObject\childrenIntroduction;

use backend\widgets\ActiveField;
use common\models\enum\UserType;
use common\models\reference\CardChild;
use common\models\reference\Child;
use common\models\reference\File;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use common\models\form\SystemForm;
use common\models\tablepart\SchoolClassChild;
use common\models\tablepart\ServiceObjectSchoolClass;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Yii;
use yii\base\UserException;
use yii\web\UploadedFile;

class ChildrenIntroductionUploadFile extends SystemForm
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
     * @throws SpreadsheetException
     * @throws Exception
     */
    public function proceed()
    {
        if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT) {
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
                for ($column = 'A'; $column <= 'E'; $column++) {
                    $cells[$row][] = $data->get($column . $row)->getValue();
                }
            }

            $serviceObject = ServiceObject::findOne(['user_id' => Yii::$app->user->id]);
            if ($serviceObject) {
                foreach ($cells as $row => $column) {
                    $surname = trim($column[0]) ?? null;
                    $forename = trim($column[1]) ?? null;
                    $patronymic = trim($column[2]) ?? null;
                    $classNumber = trim($column[3]) ?? null;
                    $classLitter = trim($column[4]) ?? null;

                    $schoolClass = SchoolClass::findOne(['number' => $classNumber, 'litter' => $classLitter, 'service_object_id' => $serviceObject->id]);
                    if (!$schoolClass) {
                        $schoolClass = new SchoolClass();
                        $schoolClass->number = $classNumber;
                        $schoolClass->litter = $classLitter;
                        $schoolClass->service_object_id = $serviceObject->id;
                        $schoolClass->save();
                    }

                    $serviceObjectSchoolClass = ServiceObjectSchoolClass::findOne(['parent_id' => $serviceObject->id, 'school_class_id' => $schoolClass->id]);
                    if (!$serviceObjectSchoolClass) {
                        $serviceObjectSchoolClass = new ServiceObjectSchoolClass();
                        $serviceObjectSchoolClass->parent_id = $serviceObject->id;
                        $serviceObjectSchoolClass->school_class_id = $schoolClass->id;
                        $serviceObjectSchoolClass->save();
                    }

                    $child = Child::findOne([
                        'surname' => $surname,
                        'forename' => $forename,
                        'patronymic' => $patronymic,
                        'service_object_id' => $serviceObject->id,
                        'school_class_id' => $schoolClass->id
                    ]);


                    if (!$child) {
                        $cardNumber = '';
                        $length = 10;
                        for ($i = 0; $i < $length; $i++) {
                            $cardNumber .= mt_rand(0, 9);
                        }
                        $card = new CardChild();
                        $card->card_number = $cardNumber;
                        $card->balance = 0;
                        $card->limit_per_day = 0;
                        $card->save();

                        $child = new Child();
                        $child->surname = $surname;
                        $child->forename = $forename;
                        $child->patronymic = $patronymic;
                        $child->service_object_id = $serviceObject->id;
                        $child->school_class_id = $schoolClass->id;
                        $child->card_id = $card->id;
                        $child->save();
                    }

                    $schoolClassChild = SchoolClassChild::findOne(['parent_id' => $schoolClass->id, 'child_id' => $child->id]);
                    if (!$schoolClassChild) {
                        $schoolClassChild = new SchoolClassChild();
                        $schoolClassChild->parent_id = $schoolClass->id;
                        $schoolClassChild->child_id = $child->id;
                        $schoolClassChild->save();
                    }
                }
                Yii::$app->session->setFlash('success', 'Операция прошла успешно.');
            } else {
                Yii::$app->session->setFlash('error', 'Вы не являетесь объектом обслуживания.');
            }
        }
    }
}