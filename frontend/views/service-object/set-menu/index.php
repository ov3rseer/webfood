<?php

use backend\widgets\ActiveForm;
use backend\widgets\Select2\Select2;
use frontend\models\serviceObject\SetMenuForm;
use frontend\widgets\MenuCalendar\MenuCalendar;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var SetMenuForm $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = $shortClassName . '-grid';
$formId = $shortClassName . '-form';
$pjaxId = $shortClassName . '-pjax';
$modalId = $shortClassName . '-modal';
$weekendButtonId = $shortClassName . '-weekend-button';
$editMenuButtonId = $shortClassName . '-menu-button';
$deleteMenuButtonId = $shortClassName . '-delete-menu-button';
$groupButtonId = $shortClassName . '-edit-event-buttons';

$form = ActiveForm::begin([
    'id' => $formId,
    'method' => 'POST',
]);

echo $form->errorSummary($model);

Modal::begin([
    'id' => $modalId,
    'header' => '<h3>Установка меню</h3>',
    'footer' => '<div id="' . $groupButtonId . '" class="btn-group">'
        . Html::button('', ['id' => $deleteMenuButtonId, 'class' => 'btn btn-warning'])
        . Html::submitButton('', ['id' => $editMenuButtonId, 'class' => 'btn btn-primary'])
        . Html::button('', ['id' => $weekendButtonId, 'class' => 'btn btn-danger'])
        . '</div>',
]);
?>
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'menu_id')->widget(Select2::class, [
            'pluginOptions' => [
                'placeholder' => 'Выберите значение...',
                'allowClear' => true,
            ],
            'items' => $model->menu,
        ]); ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <?= Html::label('Цикличность меню', Html::getInputName($model, 'menu_cycle_id')) ?>
        <?=
        Html::dropDownList(Html::getInputName($model, 'menu_cycle_id'), '', $model->menuCycle, [
            'id' => Html::getInputId($model, 'menu_cycle_id'),
            'class' => 'form-control'
        ])
        ?>
    </div>
</div>
<div class="row mt-3">
    <div class="col-xs-12">
        <?=
        Html::dropDownList(Html::getInputName($model, 'week_day_id'), '', $model->weekDay, [
            'id' => Html::getInputId($model, 'week_day_id'),
            'class' => 'form-control'
        ])
        ?>
    </div>
</div>
<?php

$this->registerJs("
    $('#" . $weekendButtonId . "').click(function(){
        var beginDay = $(this).attr('data-date');
        $.ajax({
            url: '" . Url::to(['edit-weekend']) . "',
            method: 'POST',
            dataType: 'json',
            data: {'beginDay': beginDay},   
            success: function(data) {
                $('#" . $modalId . "').modal('hide');
                $('#menu-calendar').fullCalendar('refetchEvents');
            }    
        });
    });
    $('#" . $deleteMenuButtonId . "').click(function(){
        var setMenuId = $(this).attr('data-set-menu-id');
        $.ajax({
            url: '" . Url::to(['delete-menu']) . "',
            method: 'POST',
            dataType: 'json',
            data: {'setMenuId': setMenuId},   
            success: function(data) {
                $('#" . $modalId . "').modal('hide');
                $('#menu-calendar').fullCalendar('refetchEvents');
            }    
        });
    });
");
Modal::end();
$form->end();
?>

<div style="width:900px;">
    <?php
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
                var beginDay = start.format();
                var endDay = end.format();            
                var date1 = new Date(beginDay);
                var date2 = new Date(endDay);             
                var daysLag = Math.ceil(Math.abs(date2.getTime() - date1.getTime()) / (1000 * 3600 * 24));
                
                $('#" . $deleteMenuButtonId . "').hide();        
                $('#" . $deleteMenuButtonId . "').text('Удалить меню');    
                $('#" . $editMenuButtonId . "').show();   
                $('#" . $editMenuButtonId . "').text('Установить меню');   
                $('#" . $weekendButtonId . "').text('Установить выходной');  
                $('#" . $weekendButtonId . "').attr('data-date', beginDay);   
                
                if(daysLag == 1){            
                    var events = $('#menu-calendar').fullCalendar('clientEvents', function(event){                    
                        if(event.start.format() == beginDay){
                            if(event.description == 'menu'){   
                                $('#" . $deleteMenuButtonId . "').show();                                         
                                $('#" . $deleteMenuButtonId . "').attr('data-set-menu-id', event.nonstandard.id);                                          
                                $('#" . $editMenuButtonId . "').text('Изменить меню');              
                            }    
                            if(event.description == 'weekend'){      
                               $('#" . $editMenuButtonId . "').hide();
                               $('#" . $weekendButtonId . "').text('Удалить выходной'); 
                            } 
                        }
                    });
                    var weekDayValue = date1.getDay();   
                    var weekDayInput = $('#" . Html::getInputId($model, 'week_day_id') . "');                    
                    weekDayInput.val(weekDayValue != 0 ? weekDayValue : 7);
                    weekDayInput.addClass('d-none');            
                    var weekDayText = weekDayInput.find('option:selected').text();          
                    $('#" . $modalId . " .modal-header').html('<h3>Установка меню: ' + weekDayText + '</h3>');
                    $('#" . $modalId . "').modal('show');
                    return;
                }
                $.ajax({
                    url: '" . Url::to(['edit-weekend']) . "',
                    method: 'POST',
                    dataType: 'json',
                    data: {'beginDay': beginDay, 'endDay': endDay},   
                    success: function(data) {
                        $('#menu-calendar').fullCalendar('refetchEvents');
                    }                          
                });
            }
        "),
        'ajaxEvents' => Url::to(['render-calendar']),
    ]);
    ?>
</div>

