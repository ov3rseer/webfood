<?php

namespace backend\widgets\IframeDialog;

use yii\web\AssetBundle;

class IframeDialogAsset extends AssetBundle
{
    public $sourcePath = '@backend/widgets/IframeDialog/assets';

    public $css = ['iframedialog.css'];

    public $js = ['iframedialog.js'];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
