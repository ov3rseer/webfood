<?php

namespace common\models\tablepart;

use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Классы" справочника "Объект обслуживания"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $school_class_id
 *
 * Отношения:
 * @property ServiceObject $parent
 * @property SchoolClass   $schoolClass
 */
class ServiceObjectSchoolClass extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['school_class_id'], 'integer'],
            [['school_class_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'school_class_id' => 'Класс',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ServiceObject::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSchoolClass()
    {
        return $this->hasOne(SchoolClass::className(), ['id' => 'school_class_id']);
    }
}