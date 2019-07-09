<?php

namespace backend\widgets;

use backend\widgets\BootstrapDateRangePicker\BootstrapDateRangePicker;
use backend\widgets\IframeDialog\IframeDialogAsset;
use backend\widgets\Select2\Select2;
use common\models\ActiveRecord;
use common\models\document\Document;
use common\models\enum\Enum;
use common\models\reference\Reference;
use ReflectionClass;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

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
     * Генерация поля для ввода даты со временем
     * @param boolean $singleDate выбор только одной даты
     * @return $this
     * @throws \Exception
     */
    public function dateTime($singleDate = true)
    {
        $this->parts['{input}'] = BootstrapDateRangePicker::widget([
            'model' => $this->model,
            'attribute' => Html::getAttributeName($this->attribute),
            'options' => $this->inputOptions,
            'clientOptions' => [
                'singleDatePicker' => $singleDate,
                'autoUpdateInput' => false,
                'timePicker' => true,
                'timePicker24Hour' => true,
                'timePickerSeconds' => true,
                'locale' => [
                    'format' => 'YYYY-MM-DD HH:mm:ss',
                    'applyLabel' => 'Применить',
                    'cancelLabel' => 'Отмена',
                    'customRangeLabel' => 'Вручную',
                ],
                'linkedCalendars' => false,
                'ranges' => [
                    'Сегодня' => [
                        new JsExpression('moment().hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().hours(23).minutes(59).seconds(59)')
                    ],
                    'Вчера' => [
                        new JsExpression('moment().subtract(1, "days").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().subtract(1, "days").hours(23).minutes(59).seconds(59)')
                    ],
                    'Текущая неделя' => [
                        new JsExpression('moment().startOf("week").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().endOf("week").hours(23).minutes(59).seconds(59)')
                    ],
                    'Прошлая неделя' => [
                        new JsExpression('moment().subtract(1, "week").startOf("week").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().subtract(1, "week").endOf("week").hours(23).minutes(59).seconds(59)')
                    ],
                    'Текущий месяц' => [
                        new JsExpression('moment().startOf("month").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().endOf("month").hours(23).minutes(59).seconds(59)')
                    ],
                    'Прошлый месяц' => [
                        new JsExpression('moment().subtract(1, "month").startOf("month").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().subtract(1, "month").endOf("month").hours(23).minutes(59).seconds(59)')
                    ],
                ],
            ],
            'clientEvents' => [
                'apply.daterangepicker' => new JsExpression('function(ev, picker) {
                    $(this).val(picker.startDate.format("YYYY-MM-DD HH:mm:ss") ' . ($singleDate ? '' : ' + " - " + picker.endDate.format("YYYY-MM-DD HH:mm:ss")'). ');
                    $(this).trigger("change");
                }'),
                'cancel.daterangepicker' => new JsExpression('function(ev, picker) {
                    $(this).val("");
                    $(this).trigger("change");
                }'),
            ],
        ]);
        return $this;
    }

    /**
     * Генерация поля для ввода даты со временем
     * @param boolean $singleDate выбор только одной даты
     * @return $this
     * @throws \Exception
     */
    public function date($singleDate = true)
    {
        $this->parts['{input}'] = BootstrapDateRangePicker::widget([
            'model' => $this->model,
            'attribute' => Html::getAttributeName($this->attribute),
            'options' => $this->inputOptions,
            'clientOptions' => [
                'singleDatePicker' => $singleDate,
                'autoUpdateInput' => false,
                'locale' => [
                    'format' => 'YYYY-MM-DD',
                    'applyLabel' => 'Применить',
                    'cancelLabel' => 'Отмена',
                    'customRangeLabel' => 'Вручную',
                ],
                'linkedCalendars' => false,
                'ranges' => [
                    'Сегодня' => [
                        new JsExpression('moment().hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().hours(23).minutes(59).seconds(59)')
                    ],
                    'Вчера' => [
                        new JsExpression('moment().subtract(1, "days").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().subtract(1, "days").hours(23).minutes(59).seconds(59)')
                    ],
                    'Текущая неделя' => [
                        new JsExpression('moment().startOf("week").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().endOf("week").hours(23).minutes(59).seconds(59)')
                    ],
                    'Прошлая неделя' => [
                        new JsExpression('moment().subtract(1, "week").startOf("week").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().subtract(1, "week").endOf("week").hours(23).minutes(59).seconds(59)')
                    ],
                    'Текущий месяц' => [
                        new JsExpression('moment().startOf("month").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().endOf("month").hours(23).minutes(59).seconds(59)')
                    ],
                    'Прошлый месяц' => [
                        new JsExpression('moment().subtract(1, "month").startOf("month").hours(0).minutes(0).seconds(0)'),
                        new JsExpression('moment().subtract(1, "month").endOf("month").hours(23).minutes(59).seconds(59)')
                    ],
                ],
            ],
            'clientEvents' => [
                'apply.daterangepicker' => new JsExpression('function(ev, picker) {
                    $(this).val(picker.startDate.format("YYYY-MM-DD") ' . ($singleDate ? '' : ' + " - " + picker.endDate.format("YYYY-MM-DD")'). ');
                    $(this).trigger("change");
                }'),
                'cancel.daterangepicker' => new JsExpression('function(ev, picker) {
                    $(this).val("");
                    $(this).trigger("change");
                }'),
            ],
        ]);
        return $this;
    }

    /**
     * Генерация поля для вывода ссылки
     * @param boolean $hasMultiselect
     * @return $this
     * @throws \Exception
     */
    public function reference($hasMultiselect = false)
    {
        $attribute = Html::getAttributeName($this->attribute);
        if ($relation = $this->model->getAttributeRelation($attribute)) {
            /** @var ActiveRecord $class */
            $class = $relation['class'];
            if (is_subclass_of($class, Enum::class, true)) {
                if (!isset($this->inputOptions['prompt'])) {
                    $this->inputOptions['prompt'] = '(не указано)';
                } else if ($this->inputOptions['prompt'] === false) {
                    unset($this->inputOptions['prompt']);
                }
                $items = isset($this->additionalOptions['items'])
                    ? $this->additionalOptions['items']
                    : $class::find()->indexBy('id')->orderBy('name')->all();
                $this->dropDownList($items, $this->inputOptions);
                $this->parts['{label}'] = Html::activeLabel($this->model, $relation['name'], $this->labelOptions);
                return $this;
            } else if (is_subclass_of($class, Reference::class, true) || is_subclass_of($class, Document::class, true)) {
                $isDocument = is_subclass_of($class, Document::class, true);
                $controllerId = '/' . ($isDocument ? 'document' : 'reference') . '/' . Inflector::camel2id((new ReflectionClass($class))->getShortName());
                $widgetConfig = [
                    'options' => array_merge($this->inputOptions, [
                        'class' => 'reference-field',
                    ]),
                    'pluginOptions' => [
                        'placeholder' => isset($this->additionalOptions['placeholder']) ? $this->additionalOptions['placeholder'] : 'Выберите значение...',
                        'allowClear' => true,
                        'width' => null,
                    ],
                ];
                $items = [];
                if ($hasMultiselect) {
                    /** @var Reference[]|Document[] $models */
                    $models = $this->model->{$relation['name']};
                    foreach ($models as $model) {
                        $items[$model->id] = (string)$model;
                    }
                    $widgetConfig = ArrayHelper::merge($widgetConfig, [
                        'options' => [
                            'multiple' => true,
                        ],
                        'name' => isset($this->inputOptions['name'])
                            ? $this->inputOptions['name'] : Html::getInputName($this->model, $this->attribute) . '[]',
                        'items' => $items,
                        'value' => array_keys($items),
                        'pluginOptions' => [
                            'multiple' => true,
                        ],
                    ]);
                } else {
                    $items[''] = '';
                    if (key_exists('value', $widgetConfig['options'])) {
                        $value = $widgetConfig['options']['value'];
                        if (is_array($value)) {
                            $items[$value[0]] = $value[1];
                        } else {
                            $items[$value] = $value;
                        }
                    } else {
                        $value = $this->model->{$attribute};
                        if ($value) {
                            $items[$value] = (string)$class::findOne($value);
                        }
                    }
                    $widgetConfig = ArrayHelper::merge($widgetConfig, [
                        'model'     => $this->model,
                        'attribute' => $this->attribute,
                    ]);
                }
                if (isset($this->additionalOptions['items']) && is_array($this->additionalOptions['items'])) {
                    $widgetConfig['items'] = ['' => ''];
                    foreach ($this->additionalOptions['items'] as $key => $val) {
                        $widgetConfig['items'][$key] = $val;
                    }
                } else {
                    $widgetConfig['items'] = $items;
                    $widgetConfig['pluginOptions']['ajax'] = array_merge([
                        'url' => (!empty($this->additionalOptions['searchUrl']) ? $this->additionalOptions['searchUrl'] : Url::to([$controllerId . '/search'])),
                        'dataType' => 'json',
                        'quietMillis' => 250,
                        'data' => new JsExpression('function(term, page) {return term;}'),
                        'processResults' => new JsExpression('function(data, page) { return { results: data }; }'),
                    ], !empty( $this->additionalOptions['ajax-options']) ? $this->additionalOptions['ajax-options'] : []);
                }
                $this->parts['{input}'] = Html::beginTag('div', ['class' => 'input-group select2-bootstrap-append']);
                $this->parts['{input}'] .= Select2::widget($widgetConfig);
                if (empty($this->additionalOptions['items'])) {
                    $this->parts['{input}'] .= Html::beginTag('div', ['class' => 'input-group-btn']);
                    $this->parts['{input}'] .= Html::a('...',
                        [
                            (!empty($this->additionalOptions['selectUrl']) ? $this->additionalOptions['selectUrl'] : $controllerId . '/select'),
                            'layout' => 'iframe'
                        ],
                        ['class' => 'btn btn-default reference-field-select']
                    );
                    $this->parts['{input}'] .= Html::endTag('div');
                }
                $this->parts['{input}'] .= Html::endTag('div');

                $view = $this->form->getView();
                IframeDialogAsset::register($view);
                $scripts = "
                    $('.reference-field-select').click(function(e) {
                        e.preventDefault();
                        $.iframedialog({url:$(this).attr('href'), opener:$(this).closest('.input-group')});
                    });
                ";
                $view->registerJs($scripts, View::POS_READY, 'reference-field');
                $this->parts['{label}'] = Html::activeLabel($this->model, $relation['name'], $this->labelOptions);
                return $this;
            }
        }
        return $this->textInput();
    }

    /**
     * Генерация поля для вывода значения без возможности ручного изменения
     * @param array $options
     * @return $this
     * @throws \ReflectionException
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
