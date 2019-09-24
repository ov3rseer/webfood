<?php

namespace frontend\models\serviceObject\childrenIntroduction;

use common\models\enum\UserType;
use common\models\reference\CardChild;
use common\models\reference\Child;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use common\models\form\SystemForm;
use common\models\tablepart\ServiceObjectSchoolClass;
use Yii;
use yii\base\UserException;

/**
 * Форма для формирования заявок на открытие счетов
 */
class ChildrenIntroductionForm extends SystemForm
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
     * @inheritdoc
     */
    public function getName()
    {
        return 'Загрузка учащихся';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        $result[self::SCENARIO_HAND_INPUT] = ['surname', 'forename', 'patronymic', 'class_number', 'class_litter'];
        $result[self::SCENARIO_UPLOAD_FILE] = [];
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'forename', 'patronymic', 'class_number', 'class_litter'], 'trim'],
            [['surname', 'forename', 'patronymic', 'class_number', 'class_litter'], 'required'],
            [['surname', 'forename', 'patronymic'], 'string', 'min' => 2, 'max' => 255],
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

                $serviceObjectSchoolClass = ServiceObjectSchoolClass::findOne(['parent_id' => $serviceObject->id, 'school_class_id' => $schoolClass->id]);
                if (!$serviceObjectSchoolClass) {
                    $serviceObjectSchoolClass = new ServiceObjectSchoolClass();
                    $serviceObjectSchoolClass->parent_id = $serviceObject->id;
                    $serviceObjectSchoolClass->school_class_id = $schoolClass->id;
                    $serviceObjectSchoolClass->save();
                }

                if (!$child) {
                    $cardNumber = '';
                    $length = 10;
                    for($i = 0; $i < $length; $i++) {
                        $cardNumber .= mt_rand(0, 9);
                    }
                    $card = new CardChild();
                    $card->card_number = $cardNumber;
                    $card->balance = 0;
                    $card->limit_per_day = 0;
                    $card->save();

                    $child = new Child();
                    $child->surname = $this->surname;
                    $child->forename = $this->forename;
                    $child->patronymic = $this->patronymic;
                    $child->service_object_id = $serviceObject->id;
                    $child->school_class_id = $schoolClass->id;
                    $child->card_id = $card->id;
                    $child->save();
                    Yii::$app->session->setFlash('success', 'Ребёнок успешно добавлен.');
                } else {
                    Yii::$app->session->setFlash('error', 'Этот ребёнок уже существует.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Вы не являетесь объектом обслуживания.');
            }
        }
    }
}