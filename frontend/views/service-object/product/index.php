<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridView;
use common\models\reference\Product;
use common\models\reference\ProductCategory;
use common\models\reference\Unit;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\form\Report $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$gridPjaxId = 'id-grid';

$units = Unit::find()->all();
$productCategories = ProductCategory::find()->all();
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
echo $form->field($model, 'price')->textInput(['class' => 'form-control']);
echo $form->field($model, 'unit_id')->dropDownList($units, ['class' => 'form-control']);
echo $form->field($model, 'product_category_id')->dropDownList($productCategories, ['class' => 'form-control']);
echo Html::submitInput('Добавить', ['class' => 'btn btn-success']);
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'col-xs-8']);
Pjax::begin();
/** @noinspection PhpUnhandledExceptionInspection */
echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => Product::find()->andWhere(['is_active' => true]),
    ]),
    'actionColumn' => false,
    'checkboxColumn' => false,
    'columns' => $model->columns,
]);
Pjax::end();
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
ActiveForm::end();