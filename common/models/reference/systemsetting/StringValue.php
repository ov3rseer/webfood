<?php

namespace common\models\reference\systemsetting;

use backend\widgets\ActiveField;
use common\models\reference\SystemSetting;

/**
 * Настройка для указания строки или текста
 */
class StringValue extends SystemSetting
{
    /**
     * @var string
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['value'], 'string'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'value' => 'Значение'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getRawValue()
    {
        return (string)$this->value;
    }

    /**
     * @inheritdoc
     */
    public function getFormattedValue()
    {
        return (string)$this->value;
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['value']['displayType'] = ActiveField::TEXT;
        }
        return $this->_fieldsOptions;
    }
}
