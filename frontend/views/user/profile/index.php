<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model Profile */

use frontend\models\user\Profile;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Мой профиль';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Здесь можно поменять свои данные, указанные при регистрации:</p>
    <p></p>
    <ul>
        <li>При изменении логина или пароля, изменятся данные для входа, рекомендуется их запомнить или записать.</li>
        <li>Для изменения данных, у вас должен быть заполнен email.</li>
    </ul>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']);

            if(!$model->email) {
                echo $form->field($model, 'email');
            }else{
                echo $form->field($model, 'name')->textInput(['autofocus' => true]);
                echo $form->field($model, 'surname')->textInput();
                echo $form->field($model, 'forename')->textInput();
                echo $form->field($model, 'email');
                echo $form->field($model, 'password')->passwordInput();
                echo $form->field($model, 'password_repeat')->passwordInput();
            }
            ?>
            <div class="form-group">
                <?= Html::submitButton('Изменить данные', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
