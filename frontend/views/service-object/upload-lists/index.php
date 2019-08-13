<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model UploadLists */

use frontend\models\serviceObject\UploadLists;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

echo Html::beginTag('div', ['class' => 'container']);
echo Html::tag('h1', Html::encode($this->title));
echo Html::tag('p', 'Здесь можно сформировать заявку на открытие счетов новичкам.');
echo Html::tag('p', 'Перед использованием сервиса ознакомьтесь с инструкцией.');

echo Html::beginTag('div', ['class' => 'input-group-btn']);
echo Html::a('Ручной ввод', '#handInput', ['data-toggle' => 'collapse', 'class' => 'btn btn-success']);
echo Html::a('Загрузка из файла', '#uploadFromFile', ['data-toggle' => 'collapse', 'class' => 'btn btn-success']);
echo Html::endTag('div');

echo Html::beginTag('div', ['id' => 'handInput', 'class' => 'collapse']);

$form = ActiveForm::begin(['id' => 'form-signup']);
echo Html::beginTag('div', ['class' => 'form-group']);

echo Html::endTag('div');
ActiveForm::end();

echo Html::endTag('div');
echo Html::endTag('div');
