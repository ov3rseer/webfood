<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridView;
use common\models\enum\ComplexType;
use common\models\enum\FoodType;
use common\models\reference\Complex;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\form\Report $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$gridPjaxId = 'id-grid';

$foodTypes = FoodType::find()->all();
$complexTypes = ComplexType::find()->all();
$form = ActiveForm::begin([
    'method' => 'POST',
    'action' => Url::to(['']),
    'enableAjaxValidation' => false,
]);
echo Html::beginTag('div', ['class' => 'report-index']);
echo Html::beginTag('div', ['class' => 'report-attributes']);
echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-xs-4']);
echo $form->field($model, 'name')->textInput(['class' => 'form-control']);
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'col-xs-4']);
echo $form->field($model, 'food_type_id')->dropDownList($foodTypes, ['class' => 'form-control']);
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'col-xs-4']);
echo $form->field($model, 'complex_type_id')->dropDownList($complexTypes, ['class' => 'form-control']);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-xs-12']);
echo $form->field($model, 'description')->textarea(['class' => 'form-control', 'style' => 'max-width:100%; min-width:100%;']);
echo Html::submitInput('Сохранить', ['class' => 'btn btn-success']);
echo Html::endTag('div');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row', 'style' => 'margin-top: 30px;']);
Pjax::begin();
/** @noinspection PhpUnhandledExceptionInspection */
echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => Complex::find()->andWhere(['is_active' => true]),
    ]),
    'actionColumn' => false,
    'checkboxColumn' => false,
    'columns' => $model->columns,
]);
Pjax::end();
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
ActiveForm::end();