<?php

namespace common\models\reference;

use common\models\tablepart\FatherChild;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Родитель"
 *
 * @property string   $forename
 * @property string   $surname
 * @property integer  $user_id
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
            [['forename', 'surname', 'patronymic'], 'string'],
            [['forename', 'surname'], 'required'],
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
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            if ($this->user_id) {
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