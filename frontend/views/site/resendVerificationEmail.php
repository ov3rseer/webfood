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
            'mouseover focus' : {
                '#reset-email-submit-btn' : {
                    'addClass' : 'highlight'
                }
            },
            'mouseout blur' : {
                '#reset-email-submit-btn' : {
                    'removeClass' : 'highlight'
                }
            }
        },
        '#reset-email-submit-btn' : {
            'mouseover focus' : {
                '#reset-email-submit-btn' : {
                    'addClass' : 'hover'
                }
            },
            'mouseout blur' : {
                '#reset-email-submit-btn' : {
                    'removeClass' : 'hover'
                }
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
                    'class' => 'hidden-btn'
                ])
                ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
