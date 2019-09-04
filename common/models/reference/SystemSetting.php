<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use common\behaviors\VirtualAttributeBehavior;
use common\models\reference\systemsetting\StringValue;
use ReflectionClass;
use yii\helpers\Json;

/**
 * Настройка системы
 *
 * Свойства:
 * @property string $data поле для хранения значения
 * @property string $name_full название настройки
 */
class SystemSetting extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Настройка системы';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Настройки системы';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ref_system_setting}}';
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : $this->name_full;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name_full'], 'string', 'max' => 1024],
            [['name'], 'unique'],
        ]);
    }

    /**
     * Получение "сырого" значения
     * @return mixed
     */
    public function getRawValue()
    {
        return Json::decode($this->data);
    }

    /**
     * Получение отформатированного значения
     * @return string
     */
    public function getFormattedValue()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name_full' => 'Понятное наименование',
            'data'      => 'Значение',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $class = new ReflectionClass($this);
        $publicProperties = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $publicProperties[] = $property->getName();
            }
        }
        return array_merge(parent::behaviors(), [
            'virtual' => [
                'class' => VirtualAttributeBehavior::className(),
                'attributes' => $publicProperties,
                'parameterName' => 'data',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function instantiate($row)
    {
        switch ($row['name']) {
            default:
                return new StringValue();
        }
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['is_active']['displayType'] = ActiveField::HIDDEN;
            $this->_fieldsOptions['name_full']['displayType'] = ActiveField::STRING;
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     */
    static public function getTagForTable()
    {
        // Метод переопределен для того, чтобы дочерние классы кешировались по имени класса SystemSetting
        return SystemSetting::className() . '#table';
    }

    /**
     * Получение значения настройки системы
     * @param string $settingName
     * @param mixed $default
     * @return mixed
     */
    static public function getSettingValue($settingName, $default = null)
    {
        /** @var SystemSetting $setting */
        $setting = SystemSetting::findOne(['name' => $settingName]);
        return $setting ? $setting->getRawValue() : $default;
    }
}
