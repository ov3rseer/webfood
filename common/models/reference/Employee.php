<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Сотрудник"
 *
 * @property string   $name_full
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
            [['name_full'], 'string', 'max' => 1024],
            [['name_full'], 'filter', 'filter' => 'trim'],
            [['service_object_id', 'user_id'], 'integer'],
            [['forename', 'surname', 'patronymic'], 'string'],
            [['forename', 'surname', 'patronymic'], 'filter', 'filter' => 'ucfirst'],
            [['forename', 'surname', 'service_object_id'], 'required'],
            [['user_id'], 'validateUser', 'skipOnEmpty' => false, 'skipOnError' => false],
        ]);
    }

    /**
     * Проверка на прикрепленного пользователя
     */
    public function validateUser()
    {
        if ($this->is_active && !$this->user_id) {
            $this->addError('summary', 'Чтобы сотрудник стал активен, необходимо прикрепить пользователя.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'              => 'ФИО',
            'name_full'         => 'ФИО полностью',
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
            if ($this->scenario != self::SCENARIO_SEARCH) {
                if ($this->user_id) {
                    $this->_fieldsOptions['user_id']['displayType'] = ActiveField::READONLY;
                } else {
                    $this->_fieldsOptions['user_id']['displayType'] = ActiveField::REFERENCE;
                }
                $this->_fieldsOptions['name_full']['displayType'] = ActiveField::READONLY;
                $this->_fieldsOptions['name']['displayType'] = ActiveField::READONLY;
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
            $this->name = $this->surname . ' ' . mb_substr($this->forename, 0, 1) . '. ' . mb_substr($this->patronymic, 0, 1) . '.';
            $this->name_full = $this->surname . ' ' . $this->forename . ' ' . $this->patronymic;
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