<?php

namespace common\models\reference;

use common\models\enum\ServiceObjectType;
use common\models\tablepart\ServiceObjectContract;
use common\models\tablepart\ServiceObjectEmployee;
use common\models\tablepart\ServiceObjectSchoolClass;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Объекты обслуживания"
 *
 * @property integer  $user_id
 * @property integer  $service_object_code
 * @property integer  $service_object_type_id
 *
 * Отношения:
 * @property User                       $user
 * @property ServiceObjectType          $serviceObjectType
 * @property ServiceObjectContract[]    $serviceObjectContracts
 * @property ServiceObjectEmployee[]    $serviceObjectEmployees
 * @property ServiceObjectSchoolClass[] $serviceObjectSchoolClasses
 */
class ServiceObject extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Объект обслуживания';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Объекты обслуживания';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_code', 'user_id'], 'integer'],
            [['service_object_code'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'user_id'                       => 'Прикреплённый пользователь',
            'service_object_code'           => 'Номер объекта обслуживания',
            'service_object_type_id'        => 'Тип объекта обслуживания',
            'serviceObjectContracts'        => 'Договора',
            'serviceObjectEmployees'        => 'Сотрудники',
            'serviceObjectSchoolClasses'    => 'Классы',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObjectType()
    {
        return $this->hasOne(ServiceObjectType::class, ['id' => 'service_object_type_id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObjectSchoolClasses()
    {
        return $this->hasMany(ServiceObjectSchoolClass::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObjectContracts()
    {
        return $this->hasMany(ServiceObjectContract::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObjectEmployees()
    {
        return $this->hasMany(ServiceObjectEmployee::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'serviceObjectSchoolClasses'    => ServiceObjectSchoolClass::class,
            'serviceObjectEmployees'        => ServiceObjectEmployee::class,
            'serviceObjectContracts'        => ServiceObjectContract::class,
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult && $this->user_id) {
            $this->is_active = true;
        } else {
            $this->is_active = false;
        }
        return $parentResult;
    }
}