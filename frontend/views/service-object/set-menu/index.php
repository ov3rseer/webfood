<?php

use backend\widgets\ActiveForm;
use backend\widgets\Select2\Select2;
use common\models\enum\DayType;
use frontend\models\serviceObject\SetMenuForm;
use frontend\widgets\MenuCalendar\MenuCalendar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var SetMenuForm $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = $shortClassName . '-grid';
$formId = $shortClassName . '-form';
$pjaxId = $shortClassName . '-pjax';

$form = ActiveForm::begin([
    'id' => $formId,
    'method' => 'POST'
]);
?>
<div class="row">
    <div class="col-xs-4">
        <?= $form->field($model, 'menu_id')->widget(Select2::class, [
            'pluginOptions' => [
                'placeholder' => 'Выберите значение...',
            ],
            'items' => $model->menu,
        ]); ?>
    </div>
    <div class="col-xs-4">
        <?= Html::label('Цикличность меню', Html::getInputName($model, 'menu_cycle_id')) ?>
        <?=
        Html::dropDownList(Html::getInputName($model, 'menu_cycle_id'), '', $model->menuCycle, [
            'id' => Html::getInputId($model, 'menu_cycle_id'),
            'class' => 'form-control'
        ])
        ?>
    </div>
    <div class="col-xs-4">
        <?= Html::label('День недели', Html::getInputName($model, 'week_day_id')) ?>
        <?=
        Html::dropDownList(Html::getInputName($model, 'week_day_id'), '', $model->weekDay, [
            'id' => Html::getInputId($model, 'week_day_id'),
            'class' => 'form-control'
        ])
        ?>
    </div>
</div>
<?php
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
$form->end();
?>

<div style="width:800px;">
    <?php
    $pjax = Pjax::begin([
        'id' => $pjaxId,
    ]);
    echo MenuCalendar::widget([
        'id' => 'menu-calendar',
        'options' => [
            'lang' => 'ru',
        ],
        'header' => [
            'left' => '',
            'center' => 'title',
            'right' => 'prev today next',
        ],
        'clientOptions' => [
            'theme' => true,
            'themeSystem' => 'bootstrap3',
            'weekends' => true,
            'selectable' => true,
        ],
        'select' => new JSExpression("
            function(start, end, allDay, jsEvent, view){                
                var events = $('#menu-calendar').fullCalendar('clientEvents');
                var beginDay = start.format();
                var endDay = end.format(); 
                var dates = [];              
                $.each(events, function(index, event){           
                    var dayTypeId = event.id.day_type_id;
                    if(beginDay <= event.id.date && endDay > event.id.date & dayTypeId == '" . DayType::WEEKEND . "'){
                        dates.push(event.id.date);
                    }
                });
                $.ajax({
                    url: '" . Url::to(['add-weekend']) . "',
                    method: 'POST',
                    dataType: 'json',
                    data: {'beginDay': beginDay, 'endDay': endDay},   
                    success: function(data) {
                        $('#menu-calendar').fullCalendar('refetchEvents');
                    }                          
                });
            }
        "),
        'eventClick' => new JsExpression("
            function(event, jsEvent, view) {
                console.log(event);
                var date = event.start.format();
                $.ajax({
                    url: '" . Url::to(['delete-weekend']) . "',
                    method: 'POST',
                    dataType: 'json',
                    data: {'date': date},   
                    success: function(data) {
                        $('#menu-calendar').fullCalendar('refetchEvents');
                    }                          
                });
              
            }
        "),
        'ajaxEvents' => Url::to(['render-calendar']),
    ]);
    $pjax->end();
    ?>
</div>

