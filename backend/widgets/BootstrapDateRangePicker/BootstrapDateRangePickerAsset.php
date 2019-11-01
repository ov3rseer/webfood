<?php

namespace backend\widgets\BootstrapDateRangePicker;

use yii\web\AssetBundle;

class BootstrapDateRangePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-daterangepicker';

    public $js = [
        'daterangepicker.js',
    ];

    public $css = [
        'daterangepicker.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\MomentAsset',
        'common\assets\MomentLocaleAsset',
    ];
}
