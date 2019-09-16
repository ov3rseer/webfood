<?php

use backend\controllers\system\SystemController;
use common\models\reference\Meal;
use terminal\models\MealForm;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;
use backend\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var MealForm $model */

/** @var SystemController $controller */
$controller = $this->context;
$this->title = $model->getName();
$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$formId = $shortClassName . '-form';
if ($info = Yii::$app->session->getFlash('proceed')) {
    ?>
    <div class="alert alert-info" role="alert">
        <?= $info ?>
    </div>
    <?php
}

echo Html::a('Назад', ['site/index', 'categoryId' => $model->meal->meal_category_id], ['class' => 'btn btn-success']);
echo Html::tag('h3', $this->title);
$form = ActiveForm::begin();

$removeMealButton = 'remove-meal-button';
$addMealButton = 'add-meal-button';
$addFiveMealButton = 'add-five-meal-button';
$addTenMealButton = 'add-ten-meal-button';
$mealQuantityInput = 'meal-quantity-input';
echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-3']);
echo Html::tag('p', '<strong>Описание: </strong>' . $model->meal->description);
echo Html::tag('p', '<strong>Цена: </strong>' . $model->meal->price . ' &#8381');
echo Html::endTag('div');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-3']);
echo Html::beginTag('div', ['class' => 'input-group input-group-lg']);
echo Html::tag('span', '<span class="glyphicon glyphicon-minus"></span>', ['id' => $removeMealButton, 'class' => 'input-group-addon btn btn-success']);
echo Html::input('number', Html::getInputName($model, 'quantity'), 1, ['id' => $mealQuantityInput, 'class' => 'form-control', 'readonly' => true, 'min' => 1]);
echo Html::tag('span', '<span class="glyphicon glyphicon-plus"></span>', ['id' => $addMealButton, 'class' => 'input-group-addon btn btn-success']);
//echo Html::tag('span', '+5', ['id' => $addFiveMealButton, 'class' => 'input-group-addon btn btn-success']);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'row mt-4']);
echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-3']);
echo Html::tag('p', '<strong>Сумма: </strong><span id="sum">' . $model->meal->price . '</span> &#8381', ['']);
echo Html::submitButton('Добавить блюдо', ['class' => 'btn btn-success']);
echo Html::endTag('div');
echo Html::endTag('div');

$this->registerJs("
    $('#" . $mealQuantityInput . "').on('change', function(){
        var quantity = this.value;
        var price = " . $model->meal->price . "
        $('#sum').text((quantity * price).toFixed(2));
    });
    $('#" . $addMealButton . "').click(function(){
        $('#meal-quantity-input')[0].stepUp();
        $('#" . $mealQuantityInput . "').trigger('change');         
    });     
//    $('#" . $addFiveMealButton . "').click(function(){
//        var i = 0;
//        while(i < 5){
//            $('#" . $mealQuantityInput . "')[0].stepUp();    
//            i++;     
//        }
//        $('#" . $mealQuantityInput . "').trigger('change');    
//    });    
    $('#" . $removeMealButton . "') . click(function () {
        $('#" . $mealQuantityInput . "')[0] . stepDown();
         $('#" . $mealQuantityInput . "').trigger('change');
    });
");
$form->end();
