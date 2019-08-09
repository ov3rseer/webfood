<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model ResetPasswordForm */

use frontend\models\ResetPasswordForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Сброс пароля';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, выберите новый пароль:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password_repeat')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
