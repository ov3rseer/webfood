<?php

use backend\widgets\ActiveForm;
use frontend\models\serviceObject\UploadLists;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var UploadLists $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$handInputButtonId = 'hand-input-button';
$handInputModalId = 'hand-input-modal';
$uploadFileButtonId = 'upload-file-button';
$uploadFileModalId = 'upload-file-modal';

echo Html::beginTag('div', ['class' => 'container']);
echo Html::tag('h1', Html::encode($this->title));
echo Html::tag('p', 'Здесь можно сформировать заявку на открытие счетов новичкам.');
echo Html::tag('p', 'Перед использованием сервиса ознакомьтесь с инструкцией.');

echo Html::beginTag('div', ['class' => 'input-group-btn']);
echo Html::a('Ручной ввод', '#', ['id' => $handInputButtonId, 'class' => 'btn btn-success']);
echo Html::a('Загрузка из файла', '#', ['id' => $uploadFileButtonId, 'class' => 'btn btn-success']);
echo Html::endTag('div');
echo Html::endTag('div');

$this->registerJs("
    $('#" . $handInputButtonId . "').click(function(e){
        e.preventDefault();
        e.stopPropagation();   
        $('#" . $handInputModalId . "').modal('show');
    });
    $('#" . $uploadFileButtonId . "').click(function(e){
        e.preventDefault();
        e.stopPropagation();   
        $('#" . $uploadFileModalId . "').modal('show');
    });
");



Modal::begin([
    'header' => '<h2>Ручной ввод</h2>',
    'options' => [
        'id' => $handInputModalId,
    ]
]);
$form = ActiveForm::begin([
    'id' => 'form-signup',
]);
echo Html::beginTag('div', ['class' => 'form-group']);
echo $form->field($model, 'surname')->textInput();
echo $form->field($model, 'forename')->textInput();
echo $form->field($model, 'patronymic')->textInput();
echo $form->field($model, 'class_number')->textInput(['type' => 'number']);
echo $form->field($model, 'class_litter')->textInput();
echo $form->field($model, 'codeword')->textInput();
echo $form->field($model, 'snils')->textInput();
echo Html::submitButton('Сохранить данные', [
    'name' => 'action',
    'value' => 'hand-input',
    'class' => 'btn btn-success',
]);
echo Html::endTag('div');
ActiveForm::end();
Modal::end();



Modal::begin([
    'header' => '<h2>Загрузка из файла</h2>',
    'options' => [
        'id' => $uploadFileModalId,
    ]
]);
$form = ActiveForm::begin([
    'id' => 'form-signup',
]);
echo Html::beginTag('div', ['class' => 'form-group']);
echo $form->field($model, 'uploadedFile')->fileInput();
echo Html::submitButton('Загрузить данные из файла', [
    'name' => 'action',
    'value' => 'upload-file',
    'class' => 'btn btn-success',
]);
echo Html::endTag('div');
ActiveForm::end();
Modal::end();