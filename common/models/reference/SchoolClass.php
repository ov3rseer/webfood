<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use common\models\tablepart\SchoolClassChild;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Класс"
 *
 * @property integer  $service_object_id
 * @property integer  $teacher_id
 * @property integer  $number
 * @property string   $litter
 *
 * Отношения:
 * @property ServiceObject      $serviceObject
 * @property SchoolClassChild[] $schoolClassChildren
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
            [['number'], 'in', 'range' => range(1, 11), 'message' => 'Значение не должно быть больше 11.'],
            [['litter'], 'string', 'max' => 1],
            [['service_object_id', 'number', 'litter'], 'required'],
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
            'service_object_id'     => 'Объект обслуживания',
            'schoolClassChildren'   => 'Ученики',
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
    public function getSchoolClassChildren()
    {
        return $this->hasMany(SchoolClassChild::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
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
            } else {
                $this->_fieldsOptions['name_full']['displayType'] = ActiveField::STRING;
            }
        }
        return $this->_fieldsOptions;
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

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            $this->name = $this->number . $this->litter;
            $this->name_full = $this->name . ' ' . $this->serviceObject;
        }
        return $parentResult;
    }
}