<?php


namespace common\models\reference;

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
}