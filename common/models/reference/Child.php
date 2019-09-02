<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Ребенок"
 *
 * @property string   $forename
 * @property string   $surname
 * @property string   $patronymic
 * @property integer  $service_object_id
 * @property integer  $school_class_id
 * @property integer  $father_id
 *
 * Отношения:
 * @property ServiceObject  $serviceObject
 * @property SchoolClass    $schoolClass
 * @property Father         $father
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
            [['forename', 'surname', 'patronymic'], 'string'],
            [['service_object_id', 'school_class_id', 'father_id'], 'integer'],
            [['forename', 'surname', 'patronymic', 'service_object_id', 'school_class_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name_full'         => 'ФИО полностью',
            'name'              => 'ФИО',
            'forename'          => 'Имя',
            'surname'           => 'Фамилия',
            'patronymic'        => 'Отчество',
            'service_object_id' => 'Объект обслуживания',
            'school_class_id'   => 'Класс',
            'father_id'         => 'Родитель',
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
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            if ($this->scenario == self::SCENARIO_SEARCH) {
                $this->_fieldsOptions['name_full']['displayType'] = ActiveField::STRING;
            } else {
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
            if (!$this->name_full && get_class($this) != User::class) {
                $this->name_full = $this->surname . ' ' . $this->forename . '. ' . $this->patronymic . '.';
                $this->name = $this->surname . ' ' . substr($this->forename, 0, 1) . ' ' . substr($this->patronymic, 0, 1);
            }
            return true;
        }
        return false;
    }
}