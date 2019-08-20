<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Отправить письмо с подтверждением';

$this->registerJs("
$().ready(function() {

    var resetEmailFluent = new FluentUI({
        '#reset-email-container, #reset-email-container input' : {
            'mouseover focus' : function() {
                let el = $('#reset-email-submit-btn');
                el.addClass('highlight'); 
            },
            'mouseout blur' : function() {
                let el = $('#reset-email-submit-btn');
                el.removeClass('highlight'); 
            }
        },
        '#reset-email-submit-btn' : {
            'mouseover focus' : function() {
                let el = $('#reset-email-submit-btn');
                el.addClass('hover'); 
            },
            'mouseout blur' : function() {
                let el = $('#reset-email-submit-btn');
                el.removeClass('hover'); 
            },
            'valid-resend-verification-email-form' : function() {
                let el = $('#reset-email-submit-btn');
                el.addClass('success');
                el.removeClass('disabled');
                el.removeAttr('disabled');
            },
            'invalid-resend-verification-email-form' : function() {
                let el = $('#reset-email-submit-btn');
                el.removeClass('success');
                el.addClass('disabled');
                el.attr({'disabled' : true});
            }
        }
    });

});
");

?>
<div id="reset-email-container" class="container">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните вашу электронную почту. Письмо с подтверждением будет отправлено туда.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'resend-verification-email-form']); ?>

            <?= $form->field($model, 'email')->textInput() ?>

            <div class="form-group">
                <?=
                Html::submitButton('Отправить', [
                    'id' => 'reset-email-submit-btn',
                    'class' => 'hidden-btn disabled',
                    'disabled' => true
                ])
                ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
