<?php

namespace backend\widgets\BootstrapDateRangePicker;

use yii\bootstrap\InputWidget;
use yii\helpers\Html;

/**
 * Виджет для выбора диапазона дат
 */
class BootstrapDateRangePicker extends InputWidget
{
    public $options = [
        'class' => 'form-control',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        BootstrapDateRangePickerAsset::register($this->view);
        $this->registerPlugin('daterangepicker');
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            return Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            return Html::textInput($this->name, $this->value, $this->options);
        }
    }
}
