<?php

namespace backend\widgets\Select2;

use yii\web\JsExpression;
use yii\widgets\InputWidget;
use yii\helpers\Json;
use yii\helpers\Html;

/**
 * Select2 widget
 * Widget wrapper for {@link https://select2.github.io/ Select2}.
 *
 * Usage:
 * ~~~
 * echo $form->field($model, 'field')->widget(Select2::className(), [
 *     'pluginOptions' => [
 *         'multiple' => true,
 * 		   'placeholder' => 'Choose item'
 *     ],
 *     'items' => [
 *         'item1',
 *         'item2',
 *         ...
 *     ],
 *     'events' => [
 *         'select2-open' => 'function (e) { log("select2:open", e); }',
 *         'select2-close' => new JsExpression('function (e) { log("select2:close", e); }')
 *         ...
 *     ]
 * ]);
 * ~~~
 */
class Select2 extends InputWidget
{
    /**
     * @var array
     */
    public $pluginOptions = [];

    /**
     * @var array
     */
    public $items = [];

    /**
     * @var array
     */
    public $events = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }
    }

    /**
     * Register widget asset.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $selector = '#' . $this->options['id'];

        Select2Asset::register($view);
        $themeAssetBundle = Select2CustomAsset::register($view);
        $this->pluginOptions['theme'] = $themeAssetBundle->themeName;

        $settings = Json::encode($this->pluginOptions);
        $js = ["jQuery('$selector').select2($settings);"];

        foreach ($this->events as $event => $callbacks) {
            $callbacks = is_array($callbacks) ? $callbacks : [$callbacks];
            foreach ($callbacks as $callback) {
                if (!$callback instanceof JsExpression) {
                    $callback = new JsExpression($callback);
                }
                $js[] = "jQuery('$selector').on('$event', $callback);";
            }
        }

        $view->registerJs(implode("\n", $js), $view::POS_READY, 'select2#' . $this->options['id']);
    }
}
