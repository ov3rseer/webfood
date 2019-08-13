<?php

namespace common\models\reference;

use yii\base\UserException;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Сотрудник"
 *
 * @property string   $forename
 * @property string   $surname
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
            [['forename', 'surname'], 'string'],
            [['forename', 'surname', 'service_object_id'], 'required'],
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
     * @throws UserException
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            if ($this->user_id) {
                if ($this->user->getProfile()) {
                    throw new UserException('Этот пользователь уже занят');
                }
                $this->is_active = true;
            } else {
                $this->is_active = false;
            }
            if ($this->surname || $this->forename) {
                $this->name_full = $this->surname . ' ' . $this->forename;
            }
        }
        return $parentResult;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->user) {
            $this->user->name_full = $this->name_full;
            $this->user->save();
        }
    }
}