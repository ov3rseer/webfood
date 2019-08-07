<?php

namespace common\models\tablepart;

use common\models\reference\Employee;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Сотрудник" справочника "Объект обслуживания"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $employee_id
 *
 * Отношения:
 * @property ServiceObject $parent
 * @property Employee      $employee
 */
class ServiceObjectEmployee extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['employee_id'], 'integer'],
            [['employee_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'employee_id' => 'Сотрудник',
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
    public function getEmployee()
    {
        return $this->hasOne(SchoolClass::className(), ['id' => 'employee_id']);
    }
}