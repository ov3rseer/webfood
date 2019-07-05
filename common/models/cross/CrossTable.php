<?php

namespace common\models\cross;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\ActiveRecord;
use common\models\reference\User;
use yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Базовая модель кросс-таблицы
 *
 * @property integer $create_user_id
 * @property DateTime $create_date
 *
 * Отношения:
 * @property User $createUser
 */
abstract class CrossTable extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'cross_';

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function behaviors()
    {
        $result = [];
        if (User::isBackendUser()) {
            $result['blameable'] = [
                'class'              => BlameableBehavior::class,
                'createdByAttribute' => $this->hasAttribute('create_user_id') ? 'create_user_id' : false,
                'updatedByAttribute' => false,
                'value'              => Yii::$app->user->id,
            ];
        }
        $result['timestamp'] = [
            'class'              => TimestampBehavior::class,
            'createdAtAttribute' => $this->hasAttribute('create_date') ? 'create_date' : false,
            'updatedAtAttribute' => false,
            'value'              => new yii\db\Expression('NOW()'),
        ];
        return array_merge(parent::behaviors(), $result);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'create_date'    => 'Дата создания',
            'create_user_id' => 'Автор',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::class, ['id' => 'create_user_id']);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['create_user_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['create_date']['displayType'] = ActiveField::READONLY;
        }
        return $this->_fieldsOptions;
    }
}
