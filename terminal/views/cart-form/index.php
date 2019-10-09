<?php

use backend\controllers\system\SystemController;
use backend\widgets\GridView\GridView;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\reference\Meal;
use terminal\models\Cart;
use terminal\models\MealForm;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\ButtonGroup;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use backend\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var Cart $model */
/* @var array $columns */
/* @var array $dataProvider */


$controller = $this->context;
$this->title = $model->getName();

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$formId = $shortClassName . '-form';
$gridWidgetId = $shortClassName . '-grid';

$totalSum = 0;

$bodySum = [

];

$footerSum = [
    'footer' => $totalSum,
];

$sum = array_merge($bodySum, $footerSum);

echo GridView::widget([
    'id' => $gridWidgetId,
    'layout' => "{items}\n{pager}",
    'options' => ['style' => 'width:auto; max-width:100%;'],
    'dataProvider' => $dataProvider,
    'columns' => [
        'counter' => [
            'header' => '№',
            'headerOptions' => ['style' => 'width:28px;'],
            'class' => SerialColumn::class,
        ],
        [
            'header' => 'Действия',
            'headerOptions' => ['style' => 'width:28px;'],
            'class' => ActionColumn::class,
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $quantity, $mealId) use ($gridWidgetId) {
                    $this->registerJs("
                            $('#" . $gridWidgetId . "-grid #delete-row-" . $mealId . "').click(function(e){              
                                if (!confirm('Вы действительно хотите удалить элемент?')) {
                                    return;
                                }                           
                                $.ajax({
                                    url: 'delete-meal',
                                    method: 'POST',
                                    dataType: 'json',
                                    data: {mealId: " . $mealId . "},                          
                                });             
                            });
                        ");
                    return Html::a('Удалить <span class="glyphicon glyphicon-remove"></span>', 'delete-meal', [
                        'id' => 'delete-row-' . $mealId,
                        'class' => 'btn btn-danger',
                        'title' => 'Удалить строку',
                    ]);
                },
            ],
        ],
        'meal' => [
            'header' => 'Товар',
            'format' => 'raw',
            'value' => function ($quantity, $mealId) {
                if (isset($mealId)) {
                    $meal = Meal::findOne(['id' => $mealId]);
                    if ($meal) {
                        return Html::encode($meal);
                    }
                }
                return '';
            },
        ],
        'price' => [
            'header' => 'Цена',
            'format' => 'raw',
            'value' => function ($quantity, $mealId) {
                if (isset($mealId)) {
                    $meal = Meal::findOne(['id' => $mealId]);
                    if ($meal && $meal->price) {
                        return Html::encode($meal->price);
                    }
                }
                return '';
            },
        ],
        'quantity' => [
            'header' => 'Количество',
            'format' => 'raw',
            'value' => function ($quantity, $mealId) {
                if (isset($quantity)) {
                    return Html::encode($quantity);
                }
                return '';
            },
        ],
        'sum' => [
            'header' => 'Сумма',
            'format' => 'raw',
            'value' => function ($quantity, $mealId) use (&$totalSum) {
                if (isset($mealId) && isset($quantity)) {
                    $meal = Meal::findOne(['id' => $mealId]);
                    if ($meal) {
                        $sum = $meal->price * $quantity;
                        $totalSum = $totalSum + $sum;
                        return Html::encode(number_format($sum, 2));
                    }
                }
                return '';
            },
        ],
    ],
    'checkboxColumn' => false,
    'showFooter' => true,
]);
