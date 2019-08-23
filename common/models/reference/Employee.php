<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use yii\base\UserException;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Сотрудник"
 *
 * @property string   $forename
 * @property string   $surname
 * @property string   $patronymic
 * @property integer  $service_object_id
 * @property integer  $user_id
 *
 * Отношения:
 * @property ServiceObject  $serviceObject
 * @property User           $user
 */
class Employee extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Сотрудник';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Сотрудники';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_id', 'user_id'], 'integer'],
            [['forename', 'surname', 'patronymic'], 'string'],
            [['forename', 'surname', 'service_object_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'              => 'ФИО',
            'forename'          => 'Имя',
            'surname'           => 'Фамилия',
            'patronymic'        => 'Отчество',
            'service_object_id' => 'Объект обслуживания',
            'user_id'           => 'Прикрепленный пользователь',
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
    public function getServiceObject()
    {
        return $this->hasOne(ServiceObject::class, ['id' => 'service_object_id']);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            if ($this->user_id) {
                $this->_fieldsOptions['user_id']['displayType'] = ActiveField::READONLY;
            } else {
                $this->_fieldsOptions['user_id']['displayType'] = ActiveField::REFERENCE;
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            if ($this->getOldAttribute('user_id') != $this->user_id) {
                throw new UserException('Пользователь уже прикреплен, изменение невозможно');
            }
            if ($this->user_id) {
                $this->is_active = true;
            } else {
                $this->is_active = false;
            }
            if ($this->surname || $this->forename) {
                if ($this->surname || $this->forename) {
                    $nameFull = $this->surname . ' ' . $this->forename. ' ' .$this->patronymic;
                    $this->name_full = $nameFull;
                    $this->name = $nameFull;
                }
            }
        }
        return $parentResult;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->user) {
            $this->user->name_full = $this->name;
            $this->user->save();
        }
    }
}