<?php

namespace common\models\reference;

use common\models\tablepart\FatherChild;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Родитель"
 *
 * @property string   $forename
 * @property string   $surname
 * @property integer  $service_object_id
 * @property integer  $user_id
 *
 * Отношения:
 * @property User           $user
 * @property ServiceObject  $serviceObject
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
    public function getServiceObject()
    {
        return $this->hasOne(ServiceObject::class, ['id' => 'service_object_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFatherChildren()
    {
        return $this->hasMany(FatherChild::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'fatherChildren' => FatherChild::className(),
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