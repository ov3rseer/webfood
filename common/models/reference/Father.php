<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use common\models\tablepart\FatherChild;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Родитель"
 *
 * @property string  $name_full
 * @property string  $forename
 * @property string  $surname
 * @property string  $patronymic
 * @property integer $user_id
 *
 * Отношения:
 * @property User           $user
 * @property FatherChild[]  $fatherChildren
 */
class Father extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Родитель';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Родители';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['user_id'], 'integer'],
            [['name_full'], 'string', 'max' => 1024],
            [['name_full'], 'filter', 'filter' => 'trim'],
            [['forename', 'surname', 'patronymic'], 'string'],
            [['forename', 'surname', 'patronymic'], 'filter', 'filter' => 'ucfirst'],
            [['forename', 'surname'], 'required'],
        ]);
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
            'user_id'           => 'Прикрепленный пользователь',
            'fatherChildren'    => 'Дети',
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
    public function getFatherChildren()
    {
        return $this->hasMany(FatherChild::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'fatherChildren' => FatherChild::class,
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