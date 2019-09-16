<?php

namespace frontend\models\serviceObject\openCard;

use common\components\DateTime;
use common\models\document\OpenCard;
use common\models\enum\DocumentStatus;
use common\models\enum\UserType;
use common\models\reference\Child;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use common\models\tablepart\OpenCardChild;
use common\models\form\SystemForm;
use Yii;
use yii\base\UserException;

/**
 * Форма для формирования заявок на открытие счетов
 */
class OpenCardForm extends SystemForm
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
        $result[self::SCENARIO_UPLOAD_FILE] = [];
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
        ]);
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function proceed()
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

                    $openBankAccount = new OpenCard();
                    $openBankAccount->date = new DateTime('now');
                    $openBankAccount->status_id = DocumentStatus::DRAFT;
                    $openBankAccount->service_object_id = $serviceObject->id;
                    $openBankAccount->save();
                    $openBankAccountChild = new OpenCardChild();
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
}