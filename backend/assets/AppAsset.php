<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'common\assets\Html5ShivAsset',
        'common\assets\RespondAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'backend\widgets\IframeDialog\IframeDialogAsset',
        'backend\widgets\Select2\Select2CustomAsset',
        'backend\widgets\BootstrapDateRangePicker\BootstrapDateRangePickerAsset',
        'common\assets\MainAsset',
    ];
}
