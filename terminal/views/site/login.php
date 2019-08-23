<?php

/* @var $this yii\web\View */

/* @var $model \terminal\models\TerminalForm */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'WebFood';

?>

<div class="site-login">

    <div class="jumbotron">
        <h1 class="text-left">Приложите карту</h1>
        <div class="row">
            <div class="col-xs-6">
                <?php $form = ActiveForm::begin(['id' => 'card-login-form', 'class' => 'my-5']); ?>
                <?= $form->field($model, 'cardNumber')->textInput(['placeholder' => 'Номер карты']) ?>
                <div class="form-group">
                    <?= Html::submitButton('Go!', ['class' => 'btn btn-primary', 'name' => 'card-login-btn']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

</div>