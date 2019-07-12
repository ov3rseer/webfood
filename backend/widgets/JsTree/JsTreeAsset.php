<?php

namespace backend\widgets\JsTree;

use yii\web\AssetBundle;

class JsTreeAsset extends AssetBundle
{
    public $sourcePath = '@bower/jstree/dist';

    public $js = ['jstree.min.js'];

    public $css = ['themes/default/style.min.css'];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
