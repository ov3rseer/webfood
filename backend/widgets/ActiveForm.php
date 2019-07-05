<?php

namespace backend\widgets;

use backend\models\form\Form;
use common\models\ActiveRecord;

class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'backend\widgets\ActiveField';

    /**
     * @inheritdoc
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = [])
    {
        /** @var ActiveField $result */
        $result = parent::field($model, $attribute, $options);
        return $result;
    }

    /**
     * Вывод поля формы с автоопределением типа поля
     * @param ActiveRecord|Form $model
     * @param string $attribute
     * @param array $options
     * @return ActiveField|boolean
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function autoField($model, $attribute, $options = [])
    {
        if ($model->hasAttribute($attribute) && !$model->isAttributeSafe($attribute)) {
            return false;
        }
        unset($options['type'], $options['displayType']);
        $fieldOptions = $model->getFieldOptions($attribute);
        if (isset($fieldOptions['additionalOptions'])) {
            $options['additionalOptions'] = $fieldOptions['additionalOptions'];
        }
        /** @var ActiveField $result */
        $result = $this->field($model, $attribute, $options);
        switch ($fieldOptions['displayType']) {
            case ActiveField::TEXT:
                $result->textarea();
                break;
            case ActiveField::BOOL:
                $result->checkbox();
                break;
            case ActiveField::HIDDEN:
                $result->template = '{input}';
                $result->hiddenInput();
                break;
            case ActiveField::READONLY:
                $result->readonly();
                break;
            case ActiveField::FILE:
                $result->fileInput();
                break;
            case ActiveField::TIMESTAMP:
            case ActiveField::DATETIME:
                $result->dateTime();
                break;
            case ActiveField::ENUM:
            case ActiveField::REFERENCE:
                $result->reference(false);
                break;
            case ActiveField::MULTI_REFERENCE:
                $result->reference(true);
                break;
            case ActiveField::PASSWORD:
                $result->passwordInput();
                break;
            case ActiveField::IGNORE:
                $result = '';
                break;
            case ActiveField::COLOR:
                $result->textInput();
                break;
            default:
                $result->textInput();
        }
        return $result;
    }
}
