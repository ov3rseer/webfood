<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Ребенок"
 *
 * @property string  $name_full
 * @property string  $forename
 * @property string  $surname
 * @property string  $patronymic
 * @property integer $service_object_id
 * @property integer $school_class_id
 * @property integer $father_id
 * @property integer $card_id
 *
 * Отношения:
 * @property ServiceObject  $serviceObject
 * @property SchoolClass    $schoolClass
 * @property Father         $father
 * @property CardChild      $card
 */
class Child extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Ребенок';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Дети';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name_full'], 'string', 'max' => 1024],
            [['name_full'], 'filter', 'filter' => 'trim'],
            [['forename', 'surname', 'patronymic'], 'string'],
            [['forename', 'surname', 'patronymic'], 'filter', 'filter' => 'ucfirst'],
            [['service_object_id', 'school_class_id', 'father_id', 'card_id'], 'integer'],
            [['forename', 'surname', 'patronymic', 'service_object_id', 'school_class_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'              => 'ФИО',
            'name_full'         => 'ФИО полностью',
            'forename'          => 'Имя',
            'surname'           => 'Фамилия',
            'patronymic'        => 'Отчество',
            'service_object_id' => 'Объект обслуживания',
            'school_class_id'   => 'Класс',
            'father_id'         => 'Родитель',
            'card_id'           => 'Карта',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObject()
    {
        return $this->hasOne(ServiceObject::class, ['id' => 'service_object_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSchoolClass()
    {
        return $this->hasOne(SchoolClass::class, ['id' => 'school_class_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFather()
    {
        return $this->hasOne(Father::class, ['id' => 'father_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(CardChild::class, ['id' => 'card_id']);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            if ($this->scenario != self::SCENARIO_SEARCH) {
                $this->_fieldsOptions['name_full']['displayType'] = ActiveField::READONLY;
                $this->_fieldsOptions['name']['displayType'] = ActiveField::READONLY;
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->name = $this->surname . ' ' . mb_substr($this->forename, 0, 1) . '. ' . mb_substr($this->patronymic, 0, 1).'.';
            $this->name_full = $this->surname . ' ' . $this->forename . ' ' . $this->patronymic;
            return true;
        }
        return false;
    }
}