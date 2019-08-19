<?php

namespace frontend\models\serviceObject;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\document\OpenBankAccount;
use common\models\enum\DocumentStatus;
use common\models\enum\UserType;
use common\models\reference\Child;
use common\models\reference\File;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use common\models\tablepart\OpenBankAccountChild;
use frontend\models\SystemForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\web\UploadedFile;

/**
 * Форма для формирования заявок на открытие счетов
 */
class OpenCardRequest extends SystemForm
{
    /**
     * Сценарий для ручного ввода данных
     */
    const SCENARIO_HAND_INPUT = 'hand-input';

    /**
     * Сценарий для загрузки данных из файла
     */
    const SCENARIO_UPLOAD_FILE = 'upload-file';

    /**
     * @var UploadedFile загружаемый файл
     */
    public $uploadedFile;

    /**
     * @var string
     */
    public $surname;

    /**
     * @var string
     */
    public $forename;

    /**
     * @var string
     */
    public $patronymic;

    /**
     * @var string
     */
    public $class_number;

    /**
     * @var string
     */
    public $class_litter;

    /**
     * @var string
     */
    public $codeword;

    /**
     * @var string
     */
    public $snils;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Заявка на открытие счёта';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        $result[self::SCENARIO_HAND_INPUT] = ['surname', 'forename', 'patronymic', 'class_number', 'class_litter', 'codeword', 'snils'];
        $result[self::SCENARIO_UPLOAD_FILE] = ['uploadedFile'];
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'forename', 'patronymic', 'class_number', 'class_litter', 'codeword', 'snils'], 'trim'],
            [['surname', 'forename', 'patronymic', 'class_number', 'class_litter', 'codeword', 'snils'], 'required'],
            [['surname', 'forename', 'patronymic', 'codeword'], 'string', 'min' => 2, 'max' => 255],
            [['snils'], 'string', 'min' => 11, 'max' => 11],
            [['class_litter'], 'string', 'max' => 1],
            [['class_number'], 'integer'],
            [['class_number'], 'in', 'range' => range(0, 11), 'message' => 'Значение не должно быть больше 11.'],
            [['uploadedFile'], 'file', 'extensions' => 'xls, xlsx, csv', 'checkExtensionByMimeType' => false,],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'surname'       => 'Фамилия',
            'forename'      => 'Имя',
            'patronymic'    => 'Отчество',
            'class_number'  => 'Номер класса',
            'class_litter'  => 'Литера класса',
            'codeword'      => 'Кодовое слово',
            'snils'         => 'СНИЛС',
            'uploadedFile'  => 'Файл для загрузки',
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
     */
    public function submit()
    {
        if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT) {
            $serviceObject = ServiceObject::findOne(['user_id' => Yii::$app->user->id]);
            if ($serviceObject) {
                $schoolClass = SchoolClass::findOne([
                    'number' => $this->class_number,
                    'litter' => $this->class_litter,
                    'service_object_id' => $serviceObject->id
                ]);
                if (!$schoolClass) {
                    $schoolClass = new SchoolClass();
                    $schoolClass->number = $this->class_number;
                    $schoolClass->litter = $this->class_litter;
                    $schoolClass->service_object_id = $serviceObject->id;
                    $schoolClass->save();
                }

                $child = Child::findOne([
                    'surname' => $this->surname,
                    'forename' => $this->forename,
                    'patronymic' => $this->patronymic,
                    'service_object_id' => $serviceObject->id,
                    'school_class_id' => $schoolClass->id
                ]);
                if (!$child) {
                    $child = new Child();
                    $child->name = $this->surname . ' ' . $this->forename . ' ' . $this->patronymic;
                    $child->surname = $this->surname;
                    $child->forename = $this->forename;
                    $child->patronymic = $this->patronymic;
                    $child->service_object_id = $serviceObject->id;
                    $child->school_class_id = $schoolClass->id;
                    $child->save();

                    $openBankAccount = new OpenBankAccount();
                    $openBankAccount->date = new DateTime('now');
                    $openBankAccount->status_id = DocumentStatus::DRAFT;
                    $openBankAccount->service_object_id = $serviceObject->id;
                    $openBankAccount->save();
                    $openBankAccountChild = new OpenBankAccountChild();
                    $openBankAccountChild->child_id = $child->id;
                    $openBankAccountChild->parent_id = $openBankAccount->id;
                    $openBankAccountChild->codeword = $this->codeword;
                    $openBankAccountChild->snils = $this->snils;
                    $openBankAccountChild->save();
                    Yii::$app->session->setFlash('success', 'Заявка успешно оформлена.');
                } else {
                    Yii::$app->session->setFlash('error', 'На этого ребенка уже оформлена заявка.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Вы не являетесь объектом обслуживания.');
            }
        }
    }

    /**
     * @inheritdoc
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
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
                    $surname = trim($column[0]) ? trim($column[0]) : null;
                    $forename = trim($column[1]) ? trim($column[1]) : null;
                    $patronymic = trim($column[2]) ? trim($column[2]) : null;
                    $classNumber = trim($column[3]) ? trim($column[3]) : null;
                    $classLitter = trim($column[4]) ? trim($column[4]) : null;
                    $codeword = trim($column[5]) ? trim($column[5]) : null;
                    $snils = trim($column[6]) ? trim($column[6]) : null;

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
                        $child->name = $surname . ' ' . $forename . ' ' . $patronymic;
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
                    $openBankAccount = new OpenBankAccount();
                    $openBankAccount->date = new DateTime('now');
                    $openBankAccount->status_id = DocumentStatus::DRAFT;
                    $openBankAccount->service_object_id = $serviceObject->id;
                    $openBankAccount->save();
                    foreach ($children as $childId => $child) {
                        $openBankAccountChild = new OpenBankAccountChild();
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