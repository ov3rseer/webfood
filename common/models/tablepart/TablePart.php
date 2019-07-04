<?php

namespace common\models\tablepart;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\models\ActiveRecord;
use common\models\reference\User;
use ReflectionException;
use yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * Базовая модель строки табличной части
 *
 * @property integer  $parent_id
 * @property integer  $create_user_id
 * @property DateTime $create_date
 *
 * Отношения:
 * @property User $createUser
 */
abstract class TablePart extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'tab_';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['parent_id'], 'required'],
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
            'parent_id'      => 'Родитель',
            'create_date'    => 'Дата создания',
            'update_date'    => 'Дата изменения',
            'create_user_id' => 'Автор',
            'update_user_id' => 'Автор последнего изменения',
        ]);
    }

    /**
     * @return ActiveQuery
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
            $this->_fieldsOptions['parent_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['create_user_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['create_date']['displayType'] = ActiveField::READONLY;
            foreach ($this->_fieldsOptions as $option => $fieldOption) {
                if ($this->_fieldsOptions[$option]['displayType'] == ActiveField::TEXT) {
                    $this->_fieldsOptions[$option]['additionalOptions']['toggleButton'] = true;
                }
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * @return array|null
     * @throws ReflectionException
     * @throws yii\base\InvalidConfigException
     */
    public function getExportData()
    {
        $row = null;
        $attributeLabels = $this->attributeLabels();
        $fieldsOptions = $this->getFieldsOptions();
        $relations = $this->getAttributesWithRelation();
        foreach ($this->attributes() as $attribute) {
            if (in_array($attribute, ['create_user_id', 'create_date', 'id', 'parent_id'])) {
                continue;
            }
            $label = isset($attributeLabels[$attribute]) ? $attributeLabels[$attribute] : $attribute;
            if ($fieldsOptions[$attribute]['displayType'] == ActiveField::CATEGORY
                || $fieldsOptions[$attribute]['displayType'] == ActiveField::REFERENCE
                || $fieldsOptions[$attribute]['displayType'] == ActiveField::ENUM) {
                if (isset($relations[$attribute])) {
                    $row[$label] = (string)$this->{$relations[$attribute]['name']};
                }
            } else {
                $row[$label] = $this->{$attribute};
            }
        }
        return $row;
    }
}
