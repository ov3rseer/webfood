<?php

namespace terminal\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/main.js',
    ];
    public $depends = [
        //'terminal\assets\FontAwesomeAsset',
        'common\assets\Html5ShivAsset',
        'common\assets\RespondAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
