<?php

namespace common\models\register;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\ActiveRecord;
use common\models\reference\User;
use common\queries\ActiveQuery;
use yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Базовая модель регистра
 *
 * @property DateTime $date
 * @property integer  $create_user_id
 * @property DateTime $create_date
 *
 * Отношения:
 * @property User $createUser
 */
abstract class Register extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'reg_';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['date'], 'required'],
            'dateValidator' => [['date'], 'date', 'format' => 'php:' . DateTime::DB_DATETIME_FORMAT],
        ]);
    }

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
        $result['create_timestamp'] = [
            'class'              => TimestampBehavior::class,
            'createdAtAttribute' => $this->hasAttribute('create_date') ? 'create_date' : false,
            'updatedAtAttribute' => false,
            'value'              => function() {
                return $this->create_date ?: new yii\db\Expression('NOW()');
            },
        ];
        return array_merge(parent::behaviors(), $result);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date'           => 'Дата',
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

    /**
     * Получение списка измерений по умолчанию
     * @return array
     */
    static public function getDefaultDimensions()
    {
        return array(
            'date',
        );
    }

    /**
     * Получение списка измерений
     * @return array
     */
    static public function getDimensions()
    {
        return static::getDefaultDimensions();
    }

    /**
     * Получение списка особенных измерений (все измерения, кроме измерений по умолчанию)
     * @return array
     */
    static public function getSpecialDimensions()
    {
        return array_diff(static::getDimensions(), static::getDefaultDimensions());
    }

    /**
     * Получение среза последних значений в регистре на заданную дату
     *
     * @param null|string|DateTime $date
     * @param string $tableAlias1
     * @param string $tableAlias2
     * @return ActiveQuery
     * @throws yii\base\InvalidConfigException
     */
    static public function findSlice($date = null, $tableAlias1 = 't1', $tableAlias2 = 't2')
    {
        $joinCondition = $condition = ['AND'];
        $params = [];
        if ($date) {
            $joinCondition[] = $tableAlias2 . '.date <= :date';
            $condition[] = $tableAlias1 . '.date <= :date';
            $params[':date'] = $date;
        }

        foreach (static::getSpecialDimensions() as $dimension) {
            $joinCondition[] = $tableAlias1 . '.' . $dimension . ' = ' . $tableAlias2 . '.' . $dimension;
        }

        $joinCondition[] = $tableAlias2 . '.date > ' . $tableAlias1 . '.date';

        $condition[] = $tableAlias2 . '.date IS NULL';

        $query = static::find()
            ->alias($tableAlias1)
            ->leftJoin(static::tableName() . ' AS ' . $tableAlias2, $joinCondition)
            ->andWhere($condition, $params);
        return $query;
    }
}
