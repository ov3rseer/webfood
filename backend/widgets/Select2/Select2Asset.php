<?php

namespace backend\widgets\Select2;

use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/select2/dist';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/select2.min.js',
        'js/i18n/ru.js',
    ];

    /**
     * @inheritdoc
     */
    public $css = [
        'css/select2.min.css',
    ];
    
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
