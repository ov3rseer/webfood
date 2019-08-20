<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Запросить сброс пароля';

$this->registerJs("
$().ready(function() {

    var resetPassFluent = new FluentUI({
        '#reset-password-container, #reset-password-container input' : {
            'mouseover focus' : function() {
                let el = $('#reset-password-submit-btn');
                el.addClass('highlight'); 
            },
            'mouseout blur' : function() {
                let el = $('#reset-password-submit-btn');
                el.removeClass('highlight'); 
            }
        },
        '#reset-password-submit-btn' : {
            'mouseover focus' : function() {
                let el = $('#reset-password-submit-btn');
                el.addClass('hover'); 
            },
            'mouseout blur' : function() {
                let el = $('#reset-password-submit-btn');
                el.removeClass('hover'); 
            },
            'valid-request-password-reset-form' : function() {
                let el = $('#reset-password-submit-btn');
                el.addClass('success');
                el.removeClass('disabled');
                el.removeAttr('disabled');
            },
            'invalid-request-password-reset-form' : function() {
                let el = $('#reset-password-submit-btn');
                el.removeClass('success');
                el.addClass('disabled');
                el.attr({'disabled' : true});
            }
        }
    });

});
");

?>
<div id="reset-password-container" class="container">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните вашу электронную почту. Ссылка для сброса пароля будет отправлена ​​туда.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->textInput() ?>

                <div class="form-group">
                    <?=
                    Html::submitButton('Отправить', [
                        'id' => 'reset-password-submit-btn',
                        'class' => 'hidden-btn disabled',
                        'disabled' => true
                    ])
                    ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
