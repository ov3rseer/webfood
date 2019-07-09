<?php

namespace backend\widgets\Select2;

use yii\web\AssetBundle;

class Select2CustomAsset extends AssetBundle
{
    public $sourcePath = '@backend/widgets/Select2/assets';

    public $css = [
        'select2-bootstrap.css',
    ];

    public $js = [
        'select2-extend.js',
    ];

    public $themeName = 'bootstrap';

    public $depends = [
        'backend\widgets\Select2\Select2Asset'
    ];
}
