<?php

namespace common\assets;

use yii\web\AssetBundle;

class MomentLocaleAsset extends AssetBundle
{
    public $sourcePath = '@bower/moment/locale';

    public $js = [
        'ru-Ru.js',
    ];
}
