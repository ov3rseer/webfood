<?php

/* @var $this yii\web\View */

/* @var Father $father */

use common\models\reference\Father;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'WebFood';

$deleteChildButtonClass = 'delete-child-button';
$openAddMoneyModalClass = 'open-add-child-modal-button';
$addMoneyModalId = 'add-money-modal';
$addMoneyButtonId = 'add-money-button';
$moneyInputId = 'money-input';
$childNameLink = 'child-name-link';

$cards = [];
$children = [];
if ($father->fatherChildren) {
    foreach ($father->fatherChildren as $fatherChild) {
        $children[$fatherChild->child_id]['name'] = $fatherChild->child->name_full ?? $fatherChild->child->name;
        if ($fatherChild->child->serviceObject) {
            $children[$fatherChild->child_id]['serviceObject'] = Html::encode($fatherChild->child->serviceObject);
        }
        if ($fatherChild->child->schoolClass) {
            $children[$fatherChild->child_id]['schoolClass'] = Html::encode($fatherChild->child->schoolClass);
        }
        if ($fatherChild->child->card) {
            $cards[$fatherChild->child_id]['id'] = $fatherChild->child->card->id;
            $cards[$fatherChild->child_id]['card_number'] = $fatherChild->child->card->card_number;
            $cards[$fatherChild->child_id]['balance'] = $fatherChild->child->card->balance;
        }
    }
}

echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-4 col-lg-4']);
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading']);
echo $this->render('_addChildPanel');
echo Html::endTag('div'); // panel-heading
if (!empty($children)) {
    echo Html::beginTag('div', ['class' => 'list-group accordion', 'id' => 'child-list-accordion']);
    foreach ($children as $childId => $child) {
        echo Html::beginTag('div', ['class' => 'list-group-item list-group-item-action']);
        echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
        echo Html::a($child['name'], '#childInfo-wrap-' . $childId, [
            'class' => $childNameLink,
            'style' => 'text-decoration:none; border-bottom: 1px dashed #000080;',
            'data-card-id' => isset($cards[$childId]['id']) ?? 0,
        ]);
        echo Html::a('<span class="glyphicon glyphicon-minus"></span><span class="glyphicon glyphicon-user"></span>',
            '#', ['class' => 'text-danger ' . $deleteChildButtonClass, 'data' => ['child-id' => $childId]]);
        echo Html::endTag('span');

        echo Html::beginTag('div', ['id' => 'childInfo-wrap-' . $childId, 'class' => 'collapse']);
        if (isset($child['serviceObject'])) {
            echo Html::beginTag('p', ['style' => 'margin-top:10px;']);
            echo Html::tag('span', '<em class="text-muted">Школа:</em> ' . Html::encode($child['serviceObject']));
            echo Html::endTag('p');
        }
        if (isset($child['schoolClass'])) {
            echo Html::beginTag('p', ['style' => 'margin-top:10px;']);
            echo Html::tag('span', '<em class="text-muted">Класс:</em> ' . Html::encode($child['schoolClass']));
            echo Html::endTag('p');
        }
        if (!empty($cards[$childId])) {
            echo Html::beginTag('p', ['style' => 'margin-top:10px; ']);
            echo Html::tag('span', '<em class="text-muted">Номер карты:</em> ' . Html::encode($cards[$childId]['card_number']));
            echo Html::endTag('p');
            echo Html::beginTag('p', ['style' => 'display:flex; justify-content: space-between;']);
            echo Html::tag('span', '<em class="text-muted">Баланс:</em> ' . Html::encode($cards[$childId]['balance']));
            echo Html::a('Пополнить', '#', [
                'class' => 'btn btn-success ' . $openAddMoneyModalClass,
                'data' => [
                    'card-id' => $cards[$childId]['id']
                ]
            ]);
            echo Html::endTag('p');
        }
        echo Html::endTag('div');
        echo Html::endTag('div'); // list-group-item list-group-item-action
    }
    echo Html::endTag('div'); // list-group accordion
}
echo Html::endTag('div'); // panel panel-default
echo Html::endTag('div'); // col-xs-4

echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-8 col-lg-8']);
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading child-buttons-panel']);
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'panel-body list-group card-history']);
echo 'Чтобы увидеть дополнительную информацию, выберите в списке ребенка.';
echo Html::endTag('div'); // panel-body
echo Html::endTag('div'); // panel panel-default
echo Html::endTag('div'); // col-xs-12 col-sm-6 col-md-8 col-lg-8

Modal::begin([
    'header' => '<h2>Введите сумму для пополнения</h2>',
    'options' => [
        'id' => $addMoneyModalId
    ]
]);
echo '<div class="input-group">';
echo Html::input('number', null, 0, [
    'id' => $moneyInputId,
    'aria-describedby' => 'basic-addon2',
    'class' => 'form-control',
    'placeholder' => 'Введите сумму'
]);
echo '<span class="input-group-btn">';
echo Html::button('<span class="glyphicon glyphicon-ok"></span>', [
    'id' => $addMoneyButtonId,
    'class' => 'btn btn-success'
]);
echo '</span>';
echo '</div>';
Modal::end();

$this->registerJs("
    $('." . $childNameLink . "').click(function() { 
        var divs = $('#child-list-accordion').find('.collapse').hide();
        var div = $(this).attr('href'); 
        $(div).show();
        var childButtonsPanel = $('.child-buttons-panel');
        //$(childButtonsPanel).html('<button class=\"btn btn-success\">Заказать питание</button>');
        var cardId = $(this).data('card-id');
        if(cardId){
            $.ajax({
                url: '" . Url::to(['index']) . "',
                data: {'cardId' : cardId},
                dataType: 'json',
                type: 'POST',                    
                complete: function(response){   
                    $('.card-history').html(response.responseText);
                }
            });
        }
    });
    $('." . $openAddMoneyModalClass . "').click(function() {
        $('#" . $addMoneyModalId . "').modal('show');         
        var cardId = $(this).data('card-id');                    
        $('#" . $addMoneyButtonId . "').attr('data-card-id', cardId);          
    }),
    $('#" . $addMoneyButtonId . "').click(function() {           
        var cardId = $(this).data('card-id');
        var money = $('#" . $moneyInputId . "').val();    
        $.ajax({
            url: '" . Url::to(['father/money/add-money']) . "',
            data: {'cardId' : cardId, 'money': money},
            dataType: 'json',
            type: 'POST', 
            success: function() {
                location.reload();
            },            
        });
    }) 
    $('." . $deleteChildButtonClass . "').click(function(e){          
        e.stopPropagation();
        e.preventDefault();                    
        if (!confirm('Вы действительно хотите удалить ребенка?')) {
            return;
        }          
        var childId = $(this).data('child-id');
        $.ajax({
            url: '" . Url::to(['father/my-child/delete-child']) . "',
            data: {'childId' : childId},
            dataType: 'json',
            type: 'POST', 
            success: function () {
                location.reload();
            },               
            error: function (data) { 
                alert(data.responseText);                      
            },               
        });
    }); 
");