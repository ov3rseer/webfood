<?php

/* @var $this yii\web\View */

use common\models\enum\UserType;
use yii\bootstrap\Html;

$this->title = 'WebFood';

echo Html::beginTag('div', ['class' => 'jumbotron']);
if (Yii::$app->user->identity->user_type_id == UserType::ADMIN) {
    echo Html::a('Админ-панель', 'admin', [
        'class' => 'btn btn-lg btn-success',
        'style' => 'width:300px;',
    ]);
}
echo Html::endTag('div');
