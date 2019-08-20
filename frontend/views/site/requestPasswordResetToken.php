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
            'mouseover focus' : {
                '#reset-password-submit-btn' : {
                    'addClass' : 'highlight'
                }
            },
            'mouseout blur' : {
                '#reset-password-submit-btn' : {
                    'removeClass' : 'highlight'
                }
            }
        },
        '#reset-password-submit-btn' : {
            'mouseover focus' : {
                '#reset-password-submit-btn' : {
                    'addClass' : 'hover'
                }
            },
            'mouseout blur' : {
                '#reset-password-submit-btn' : {
                    'removeClass' : 'hover'
                }
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
                        'class' => 'hidden-btn'
                    ])
                    ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
