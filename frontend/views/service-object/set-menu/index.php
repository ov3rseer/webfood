<?php

use backend\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin();

$fieldsOptions = $model->getFieldsOptions();
?>
    <div class="row">
        <div class="col-xs-4">
            <?= $form->autoField($model, 'menu_id', $fieldsOptions['menu_id']) ?>
        </div>
        <div class="col-xs-4">
            <?= $form->autoField($model, 'menu_cycle_id', $fieldsOptions['menu_cycle_id']) ?>
        </div>
        <div class="col-xs-4">
            <?= $form->autoField($model, 'week_day_id', $fieldsOptions['week_day_id']) ?>
        </div>
    </div>
<?php
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
$form->end();