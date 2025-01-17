<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\reference\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p>Здравствуйте, <?= Html::encode($user->name) ?>,</p>

    <p>Перейдите по ссылке ниже, чтобы подтвердить свою электронную почту:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
