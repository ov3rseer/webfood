<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\ActiveRecord;
use common\queries\ReferenceQuery;
use yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * Базовая модель элемента справочника
 *
 * @property string   $name
 * @property string   $name_full
 * @property boolean  $is_active
 * @property integer  $create_user_id
 * @property integer  $update_user_id
 * @property DateTime $create_date
 * @property DateTime $update_date
 *
 * Отношения:
 * @property User $createUser
 * @property User $updateUser
 */
abstract class Reference extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'ref_';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'name_full'], 'filter', 'filter' => 'trim'],
            [['name'], 'string', 'max' => 256],
            [['name_full'], 'string', 'max' => 1024],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => true],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        $result[self::SCENARIO_SEARCH] = array_merge(
            $result[self::SCENARIO_SEARCH], [
                'create_user_id',
                'update_user_id',
                'create_date',
                'update_date',
            ]
        );
        return $result;
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
                'updatedByAttribute' => $this->hasAttribute('update_user_id') ? 'update_user_id' : false,
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
        $result['update_timestamp'] = [
            'class'              => TimestampBehavior::class,
            'createdAtAttribute' => false,
            'updatedAtAttribute' => $this->hasAttribute('update_date') ? 'update_date' : false,
            'skipUpdateOnClean'  => false,
            'value'              => function() {
                return $this->scenario == static::SCENARIO_SYSTEM ? $this->update_date : new yii\db\Expression('NOW()');
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
            'name'           => 'Наименование',
            'name_full'      => 'Полное наименование',
            'is_active'      => 'Активен',
            'create_date'    => 'Дата создания',
            'update_date'    => 'Дата изменения',
            'create_user_id' => 'Автор',
            'update_user_id' => 'Автор последнего изменения',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $parentResult = parent::beforeValidate();
        if ($parentResult && $this->isNewRecord && !$this->name) {
            $this->name = $this->generateNewName();
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->name_full && get_class($this) != User::class) {
                $this->name_full = $this->name;
            }
            return true;
        }
        return false;
    }

    /**
     * Генерация нового наименования элемента справочника
     * @return string
     */
    public function generateNewName()
    {
        return '';
    }

    /**
     * Магическая функция приведения объекта к строке
     * @return string
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : $this->name;
    }

    /**
     * @return ReferenceQuery|object
     * @throws yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(ReferenceQuery::class, [get_called_class()]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::class, ['id' => 'create_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdateUser()
    {
        return $this->hasOne(User::class, ['id' => 'update_user_id']);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['id']['displayType'] = ActiveField::HIDDEN;
            $this->_fieldsOptions['is_active']['displayType'] = ActiveField::BOOL;
            if ($this->scenario != self::SCENARIO_SEARCH) {
                $this->_fieldsOptions['create_user_id']['displayType'] = ActiveField::READONLY;
                $this->_fieldsOptions['update_user_id']['displayType'] = ActiveField::READONLY;
                $this->_fieldsOptions['create_date']['displayType'] = ActiveField::READONLY;
                $this->_fieldsOptions['update_date']['displayType'] = ActiveField::READONLY;
            }
            $this->_fieldsOptions['name_full']['displayType'] = ActiveField::HIDDEN;
        }
        return $this->_fieldsOptions;
    }

    /**
     * Получение URL для редактирования существующего элемента справочника
     * @param array $params
     * @return string
     */
    public function getUpdateUrl($params = [])
    {
        $path = explode('\\', static::class);
        $shortClassName = array_pop($path);
        return Url::to(array_merge(
            ['/reference/' . Inflector::camel2id($shortClassName, '-') . '/update', 'id' => $this->id],
            $params
        ));
    }
}
