<?php

namespace backend\widgets;

use common\models\form\Form;
use common\models\ActiveRecord;
use common\models\form\Report;
use yii\widgets\ActiveForm as BaseActiveForm;

class ActiveForm extends BaseActiveForm
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
        $relationData = $model->getAttributeRelation($attribute);
        $relation = $relationData ? $relationData['name'] : null;
        if (!$model->isAttributeSafe($attribute) && !$model->getRelation($attribute, false) && (!$relation || !$model->getRelation($relation, false))) {
            return false;
        }
        $fieldOptions = $model->getFieldOptions($attribute);
        if (isset($fieldOptions['additionalOptions'])) {
            $options['additionalOptions'] = $fieldOptions['additionalOptions'];
        }
        $displayType = !empty($options['displayType']) ? $options['displayType'] : $fieldOptions['displayType'];
        unset($options['type'], $options['displayType']);
        /** @var ActiveField $result */
        $result = $this->field($model, $attribute, $options);
        switch ($displayType) {
            case ActiveField::TEXT:
                $toggleButton = isset($options['additionalOptions']) && !empty($options['additionalOptions']['toggleButton']);
                $result->textarea([], $toggleButton);
                break;
            case ActiveField::BOOL:
                if ($model->scenario == $model::SCENARIO_SEARCH) {
                    $result->dropDownList(
                        ['' => '(не указано)', '0' => 'Нет', '1' => 'Да']
                    );
                } else {
                    $result->checkbox();
                }
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
                $result->dateTime($model->scenario != $model::SCENARIO_SEARCH && !($model instanceof Report));
                break;
            case ActiveField::DATE:
                $result->date($model->scenario != $model::SCENARIO_SEARCH && !($model instanceof Report));
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
            case ActiveField::DROPDOWN:
                $result->dropDownList($fieldOptions['additionalOptions']['items']);
                break;
            case ActiveField::CHECKBOX_LIST:
                $result->checkboxList($fieldOptions['additionalOptions']['items']);
                break;
            case ActiveField::SCHEDULE:
                $result->schedule();
                break;
            default:
                $result->textInput();
        }
        return $result;
    }
}
