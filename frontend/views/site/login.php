<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model LoginForm */

use common\models\LoginForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';

$this->registerJs("
$().ready(function() {

    var loginFluent = new FluentUI({
        '#login-container, #login-container input, #login-container a' : {
            'mouseover focus' : {
                '#login-submit-btn, #login-tooltip-block' : {
                    'addClass' : 'highlight'
                }
            },
            'mouseout blur' : {
                '#login-submit-btn, #login-tooltip-block' : {
                    'removeClass' : 'highlight'
                }
            }
        },
        '#login-submit-btn' : {
            'mouseover focus' : {
                '#login-submit-btn' : {
                    'addClass' : 'hover'
                }
            },
            'mouseout blur' : {
                '#login-submit-btn' : {
                    'removeClass' : 'hover'
                }
            }
        }
    });

});
");

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
                    'class' => 'hidden-btn',
                    'name' => 'login-button'
                ])
                ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
