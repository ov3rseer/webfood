<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model Profile */

use common\models\reference\Employee;
use common\models\reference\Father;
use frontend\models\user\Profile;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Мой профиль';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container">
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

            if (!$model->email) {
                echo $form->field($model, 'email');
            } else {
                echo $form->field($model, 'name')->textInput(['autofocus' => true]);
                $profile = Yii::$app->user->identity->getProfile();
                if ($profile && ($profile instanceof Father) || ($profile instanceof Employee)) {
                    echo $form->field($model, 'surname')->textInput();
                    echo $form->field($model, 'forename')->textInput();
                }
                echo $form->field($model, 'email');
                echo $form->field($model, 'password')->passwordInput();
                echo $form->field($model, 'password_repeat')->passwordInput();
            }
            ?>
            <div class="form-group">
                <?= Html::submitButton('Изменить данные', ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
