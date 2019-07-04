<?php

namespace backend\widgets;

use common\models\ActiveRecord;
use ReflectionException;
use yii\helpers\Html;

class ActiveField extends \yii\widgets\ActiveField
{
    const STRING = 'string';
    const TEXT = 'text';
    const BOOL = 'boolean';
    const SMALLINT = 'smallint';
    const INT = 'integer';
    const BIGINT = 'bigint';
    const FLOAT = 'float';
    const DECIMAL = 'decimal';
    const DATETIME = 'datetime';
    const TIMESTAMP = 'timestamp';
    const TIME = 'time';
    const DATE = 'date';
    const BINARY = 'binary';
    const MONEY = 'money';
    const HIDDEN = 'hidden';
    const READONLY = 'readonly';
    const IGNORE = 'ignore';
    const FILE = 'file';
    const REFERENCE = 'reference';
    const MULTI_REFERENCE = 'multiReference';
    const ENUM = 'enum';
    const PASSWORD = 'password';
    const HTML = 'html';
    const COLOR = 'color';
    const EMAIL = 'email';
    const DROPDOWN = 'dropdown';
    const CATEGORY = 'category';
    const CHECKBOX_LIST = 'checkboxList';
    const SCHEDULE = 'schedule';

    /**
     * @var ActiveRecord
     */
    public $model;

    /**
     * @var array дополнительные параметры для отображения поля
     */
    public $additionalOptions = [];

    /**
     * @inheritdoc
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        $options = array_merge($options, $this->inputOptions);
        Html::removeCssClass($options, 'form-control');
        parent::checkbox($options, $enclosedByLabel);
        $this->parts['{input}'] = '<div class="checkbox">' . $this->parts['{input}'] .'</div>';
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function textarea($options = [], $toggleButton = false)
    {
        $result = parent::textarea($options);
        if ($toggleButton) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
            Html::addCssClass($options,'form-control');

            $this->parts['{input}'] =
                '<div class="textarea-form">'.
                '<div class="hidden-text">' .
                '<span class="old-value">' .
                Html::encode($value) .
                '</span>'.
                Html::a('<span class="glyphicon glyphicon-pencil" style="float:right"></span>','#',
                    ['class' => 'edit-value', 'title' => 'Редактировать']) .
                '</div>' .

                '<div class="hidden-form" style="display:none">' .
                '<span class="new-value">' .
                $this->parts['{input}'] .
                '</span>'.
                Html::a('<span class="glyphicon glyphicon-ok" style="float:right"></span>', '#',
                    ['class' => 'edit-value save-value', 'title' => 'Применить']) .
                '</div>'.
                '</div>';
            $view = $this->form->getView();
            $view->registerJs(
                "$(document).on('click', '.edit-value', function(event) {
                    event.preventDefault();
                    $(this).closest('.textarea-form').find('.hidden-form, .hidden-text').toggle();            
                });
                $(document).on('click', '.save-value', function(event){
                    event.preventDefault();
                    var container = $(this).closest('.textarea-form');
                    container.find('.old-value').text(container.find('.new-value textarea').val());
                });"
            );
        }
        return $result;
    }

    /**
     * Генерация поля для вывода значения без возможности ручного изменения
     * @param array $options
     * @return $this
     * @throws ReflectionException
     */
    public function readonly($options = [])
    {
        $relation = $this->model->getAttributeRelation($this->attribute);
        $inputAddon = '';
        if ($relation) {
            $inputAddon = Html::hiddenInput(Html::getInputName($this->model, $this->attribute), $this->model->{$this->attribute});
            $this->attribute = $relation['name'];
        }
        $value = $this->model->{$this->attribute};
        if (is_bool($value)) {
            $this->checkbox(array_merge($options, ['disabled' => 'disabled']));
        } else {
            $this->textInput(array_merge($options, ['readonly' => 'readonly', 'value' => $value]));
        }
        $this->parts['{input}'] .= $inputAddon;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getClientOptions()
    {
        $result = parent::getClientOptions();
        if (isset($this->inputOptions['id'])) {
            $result['id'] = $this->inputOptions['id'];
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getInputId()
    {
        return isset($this->inputOptions['id']) ? $this->inputOptions['id'] : parent::getInputId();
    }

    /**
     * Генерация полей для ввода расписания выполнения задачи
     * @return $this
     */
    public function schedule()
    {
        $parameters = [
            'monthOfYear' => 'Месяц/месяцы года',
            'dayOfMonth' => 'День/дни месяца',
            'dayOfWeek' => 'День/дни недели',
            'hourOfDay' => 'Час/часы дня',
            'minuteOfHour' => 'Минута/минуты часа',
        ];
        $value = $this->model->{$this->attribute};
        $this->parts['{input}'] = '';
        foreach ($parameters as $parameterName => $parameterLabel) {
            $inputId = Html::getInputId($this->model, $this->attribute . '[' . $parameterName . ']');
            $inputName = Html::getInputName($this->model, $this->attribute . '[' . $parameterName . ']');
            $parameterValue = empty($value[$parameterName]) ? '' : implode(',', $value[$parameterName]);
            $this->parts['{input}'] .= Html::beginTag('div', ['class' => 'form-group']);
            $this->parts['{input}'] .= Html::tag('label', Html::encode($parameterLabel), ['class' => 'control-label', 'for' => $inputId]);
            $this->parts['{input}'] .= Html::textInput($inputName, $parameterValue, [
                'id' => $inputId,
                'class' => 'form-control',
            ]);
            $this->parts['{input}'] .= Html::endTag('div');
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hiddenInput($options = [])
    {
        $this->options['style'] = 'display:none;';
        return parent::hiddenInput($options);
    }
}
