<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use common\models\enum\ServiceObjectType;
use common\models\tablepart\ServiceObjectEmployee;
use common\models\tablepart\ServiceObjectSchoolClass;
use yii\base\UserException;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;

/**
 * Модель справочника "Объекты обслуживания"
 *
 * @property integer    $user_id
 * @property string     $city
 * @property string     $address
 * @property integer    $service_object_type_id
 *
 * Отношения:
 * @property User                       $user
 * @property ServiceObjectType          $serviceObjectType
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
            [['user_id', 'service_object_type_id'], 'integer'],
            [['city', 'address'], 'string'],
            [['city', 'address', 'service_object_type_id'], 'required'],
            [['user_id'], 'validateUser', 'skipOnEmpty' => false, 'skipOnError' => false],
        ]);
    }

    /**
     * Проверка на прикрепленного пользователя
     */
    public function validateUser()
    {
        if ($this->is_active && !$this->user_id) {
            $this->addError('summary', 'Чтобы объект обслуживания стал активен, необходимо прикрепить пользователя.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'city'                          => 'Город',
            'address'                       => 'Адрес',
            'user_id'                       => 'Прикреплённый пользователь',
            'service_object_type_id'        => 'Тип объекта обслуживания',
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
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            if ($this->scenario != self::SCENARIO_SEARCH) {
                if ($this->user_id) {
                    $this->_fieldsOptions['user_id']['displayType'] = ActiveField::READONLY;
                } else {
                    $this->_fieldsOptions['user_id']['displayType'] = ActiveField::REFERENCE;
                }
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            if ($this->user_id) {
                $this->is_active = true;
            } else {
                $this->is_active = false;
            }
        }
        return $parentResult;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws UserException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->user) {
            $this->user->name_full = $this->name;
            $this->user->save();
        }
    }
}