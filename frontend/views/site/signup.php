<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model SignupForm */

use frontend\models\SignupForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Регистрация в личном кабинете родителя';

$this->registerJs("
$().ready(function() {

    var signupFluent = new FluentUI({
        '#signup-container, #signup-container input, #signup-container a' : {
            'mouseover focus' : {
                '#signup-submit-btn, #signup-tooltip-block' : {
                    'addClass' : 'highlight'
                }
            },
            'mouseout blur' : {
                '#signup-submit-btn, #signup-tooltip-block' : {
                    'removeClass' : 'highlight'
                }
            }
        },
        '#signup-submit-btn' : {
            'mouseover focus' : {
                '#signup-submit-btn' : {
                    'addClass' : 'hover'
                }
            },
            'mouseout blur' : {
                '#signup-submit-btn' : {
                    'removeClass' : 'hover'
                }
            }
        }
    });

});
");

?>
<div class="container" id="signup-container">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните следующие поля для регистрации:</p>
    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'forename')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'surname')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'email') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'password_repeat')->passwordInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?=
        Html::submitButton('Зарегистрироваться', [
            'id' => 'signup-submit-btn',
            'class' => 'hidden-btn',
            'name' => 'signup-button'
        ])
        ?>
    </div>

    <div id="signup-tooltip-block" class="hidden-text-block my-3">
        <p>* Регистрируясь в личном кабинете вы присоединяетесь к условиям указанного Соглашения </p>
        <p>** Нажимая "Зарегистрироваться", вы разрешаете этой информационной системе использовать предоставленные вами
            данные.</p>
    </div>
    <?php ActiveForm::end(); ?>

</div>
