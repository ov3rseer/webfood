<?php

namespace common\models\reference;

use common\models\tablepart\SchoolClassChild;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Класс"
 *
 * @property integer  $service_object_id
 * @property integer  $teacher_id
 *
 * Отношения:
 * @property ServiceObject $serviceObject
 * @property Employee      $teacher
 */
class SchoolClass extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Классы';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Классы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_id', 'number'], 'integer'],
            [['number'], 'in', 'range' => range(1, 11)],
            [['litter'], 'string', 'max' => 1],
            [['service_object_id', 'litter'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'number'                => 'Номер',
            'litter'                => 'Литера',
            'teacher_id'            => 'Классный руководитель',
            'service_object_id'     => 'Объект обслуживания',
            'schoolClassChildren'   => 'Ученики',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Employee::class, ['id' => 'teacher_id']);
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
    public function getSchoolClassChildren()
    {
        return $this->hasMany(SchoolClassChild::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'schoolClassChildren' => SchoolClassChild::className(),
        ], parent::getTableParts());
    }
}