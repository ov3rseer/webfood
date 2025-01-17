<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model LoginForm */

use common\models\LoginForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';

?>

<div class="container my-3" id="login-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните следующие поля для входа:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'login')->textInput() ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <div id="login-tooltip-block" class="hidden-text-block my-3">
                Если вы забыли свой пароль, вы можете <?= Html::a('сбросить его', ['site/request-password-reset']) ?>.
                <br>
                Не пришло письмо с подтверждением? <?= Html::a('Отправить.', ['site/resend-verification-email']) ?>
            </div>

            <div class="form-group">
                <?=
                Html::submitButton('Вход', [
                    'id' => 'login-submit-btn',
                    'class' => 'btn btn-success',
                    'name' => 'login-button',
                ])
                ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
