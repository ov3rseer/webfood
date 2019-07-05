<?php

namespace common\models\document;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\ActiveRecord;
use common\models\enum\DocumentStatus;
use common\models\reference\User;
use common\queries\DocumentQuery;
use yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * Базовая модель элемента справочника
 *
 * @property string   $number
 * @property DateTime $date
 * @property integer  $status_id
 * @property string   $comment
 * @property integer  $create_user_id
 * @property integer  $update_user_id
 * @property DateTime $create_date
 * @property DateTime $update_date
 *
 * Отношения:
 * @property DocumentStatus $status
 * @property User $createUser
 * @property User $updateUser
 *
 * Методы:
 * @method DocumentQuery hasMany($class, array $link)
 * @method DocumentQuery hasOne($class, array $link)
 */
abstract class Document extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'doc_';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['shop_id'], 'default', 'value' => Yii::$app->shop->id],
            [['number'], 'filter', 'filter' => 'trim'],
            [['number'], 'string', 'max' => 255],
            [['shop_id', 'number', 'date', 'status_id'], 'required'],
            [['date'], 'date', 'format' => 'php:' . DateTime::DB_DATETIME_FORMAT],
            [['status_id'], 'integer'],
            [['comment'], 'string'],
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
                'class' => BlameableBehavior::class,
                'createdByAttribute' => $this->hasAttribute('create_user_id') ? 'create_user_id' : false,
                'updatedByAttribute' => $this->hasAttribute('update_user_id') ? 'update_user_id' : false,
                'value' => Yii::$app->user->id,
            ];
        }
        $result['timestamp'] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => $this->hasAttribute('create_date') ? 'create_date' : false,
            'updatedAtAttribute' => $this->hasAttribute('update_date') ? 'update_date' : false,
            'value' => new yii\db\Expression('NOW()'),
        ];
        return array_merge(parent::behaviors(), $result);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'shop_id' => Yii::t('common', 'Магазин'),
            'number' => Yii::t('common', 'Номер'),
            'date' => Yii::t('common', 'Дата'),
            'status_id' => Yii::t('common', 'Статус'),
            'comment' => Yii::t('common', 'Комментарий'),
            'create_date' => Yii::t('common', 'Дата создания'),
            'update_date' => Yii::t('common', 'Дата изменения'),
            'create_user_id' => Yii::t('common', 'Автор'),
            'update_user_id' => Yii::t('common', 'Автор последнего изменения'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $parentResult = parent::beforeValidate();
        if ($parentResult && $this->isNewRecord) {
            if (!$this->number) {
                $this->number = $this->generateNewNumber();
            }
            if (!$this->date) {
                $this->date = new DateTime();
            }
            if (!$this->status_id) {
                $this->status_id = DocumentStatus::DRAFT;
            }
        }
        return $parentResult;
    }

    /**
     * Генерация нового номера документа
     * @return string
     */
    abstract public function generateNewNumber();

    /**
     * Магическая функция приведения объекта к строке
     * @return string
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : $this->number . ' от ' . (string)$this->date;
    }

    /**
     * @return DocumentQuery|object
     * @throws yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(DocumentQuery::class, [get_called_class()]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::class, ['id' => 'create_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
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
            $this->_fieldsOptions['create_user_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['update_user_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['create_date']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['update_date']['displayType'] = ActiveField::READONLY;
        }
        return $this->_fieldsOptions;
    }
}
