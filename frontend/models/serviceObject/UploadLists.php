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
use Yii;
use yii\base\UserException;
use yii\web\UploadedFile;

/**
 * Форма для формирования заявок а открытие счетов
 *
 * @property File $file
 */
class UploadLists extends SystemForm
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
        return 'Загрузка списков';
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
            [['class_litter'], 'string', 'max' => 1],
            [['class_number'], 'integer'],
            [['class_number'], 'in', 'range' => range(0, 11), 'message' => 'Значение не должно быть больше 11.'],
            [['uploadedFile'], 'file', 'extensions' => 'xls, xlsx, csv', ],
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
                $schoolClass = new SchoolClass();
                $schoolClass->litter = $this->class_litter;
                $schoolClass->number = $this->class_number;
                $schoolClass->service_object_id = $serviceObject->id;
                $schoolClass->save();

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
                $openBankAccount->save();
                $openBankAccountChild = new OpenBankAccountChild();
                $openBankAccountChild->child_id = $child->id;
                $openBankAccountChild->parent_id = $openBankAccount->id;
                $openBankAccountChild->save();
            }
        }
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function proceed()
    {
        if ($this->uploadedFile && !$this->uploadedFile->error) {
            $file = new File();
            $file->setUploadFile($this->uploadedFile);
            $file->path = 'upload-lists';
            $file->save();
        }
    }
}