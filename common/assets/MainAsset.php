<?php

namespace common\assets;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $sourcePath = '@common/assets';
    public $css = [
        'css/main.css',
    ];
    public $js = [
        'js/fluent.js',
        'js/main.js',
    ];
}