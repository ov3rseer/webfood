<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var array $child */
/** @var array $card */
/** @var integer $childId */

$deleteChildButtonClass = 'delete-child-button';
$openAddMoneyModalClass = 'open-add-child-modal-button' . $childId;
$addMoneyModalId = 'add-money-modal';
$addMoneyButtonId = 'add-money-button';
$moneyInputId = 'money-input';
$childNameLink = 'child-name-link' . $childId;

echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
echo Html::a($child['name'], '#childInfo-wrap-' . $childId, [
    'class' => $childNameLink,
    'style' => 'text-decoration:none; border-bottom: 1px dashed #000080;',
    'data-card-link' => true,
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
if (!empty($card)) {
    echo Html::beginTag('p', ['style' => 'margin-top:10px; ']);
    echo Html::tag('span', '<em class="text-muted">Номер карты:</em> ' . Html::encode($card['card_number']));
    echo Html::endTag('p');
    echo Html::beginTag('p', ['style' => 'display:flex; justify-content: space-between;']);
    echo Html::tag('span', '<em class="text-muted">Баланс:</em> ' . Html::encode($card['balance']));
    echo Html::a('Пополнить', '#', [
        'class' => 'btn btn-success ' . $openAddMoneyModalClass,
        'data' => [
            'card-id' => $card['id']
        ]
    ]);
    echo Html::endTag('p');

    $this->registerJs("
        $('." . $childNameLink . "').click(function() { 
            var divs = $('#child-list-accordion').find('.collapse').hide();
            var div = $(this).attr('href'); 
            $(div).show();
            var cardId = $('." . $openAddMoneyModalClass . "').data('card-id');
            $.ajax({
                url: '" . Url::to(['index']) . "',
                data: {'cardId' : cardId},
                dataType: 'json',
                type: 'POST',                    
                complete: function(response){   
                    $('.card-history').html(response.responseText);
                }
            });
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
    ");

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
}
echo Html::endTag('div');

$this->registerJs("
    $('." . $deleteChildButtonClass . "').click(function(e){          
        e.stopPropagation();
        e.preventDefault();                    
        if (!confirm('Вы действительно хотите удалить ребенка?')) {
            return;
        }          
        var childId = $(this).data('child-id');
        $.ajax({
            url: 'father/my-child/delete-child',
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

