<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

$openAddChildModalButtonId = 'open-add-child-modal-button';
$addChildModalId = 'add-child-modal';
$searchChildInputId = 'search-child-input';
$addChildButtonId = 'add-child-button';

echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
echo Html::tag('span', 'Ваши дети', ['class' => 'panel-title h2']);
echo Html::a('<span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span>',
    '#', ['id' => $openAddChildModalButtonId, 'class' => 'text-success']);
echo Html::endTag('span');

$this->registerJs("    
    $('#" . $openAddChildModalButtonId . "').click(function(){
        $('#" . $addChildModalId . "').modal('show');
    }),
    $(document).on('input', '#" . $searchChildInputId . "', function(){  
        var userInput = $(this).val();
        $.ajax({
            url: '" . Url::to(['father/my-child/search-child']) . "',
            data: {'userInput' : userInput},
            dataType: 'html',
            type: 'POST',                           
            success: function(data) {
                $('#search-result-area').html(data);
                $('#search-result-area .list-group-item').on('click', function() {                             
                    var child = $(this).text();                                   
                    var childId = $(this).data('child-id');                                 
                    $('#" . $searchChildInputId . "').val(child);                                   
                    $('#" . $searchChildInputId . "').attr('data-child-id', childId);
                    $('#search-result-area').empty();                 
                });
            },
        });
    })
    $('#" . $addChildButtonId . "').click(function(){           
        var childId = $('#" . $searchChildInputId . "').data('child-id');
        $.ajax({
            url: 'father/my-child/add-child',
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
    }) 
");


Modal::begin([
    'header' => '<h2>Выберите своего ребенка</h2>',
    'options' => [
        'id' => $addChildModalId]
]);
echo '<div class="input-group">';
echo Html::textInput(null, null, [
    'id' => $searchChildInputId,
    'aria-describedby' => 'basic-addon2',
    'class' => 'form-control',
    'placeholder' => 'Введите ФИО ребёнка'
]);
echo '<span class="input-group-btn">';
echo Html::button('<span class="glyphicon glyphicon-ok"></span>', [
    'id' => $addChildButtonId,
    'class' => 'btn btn-success'
]);
echo '</span>';
echo '</div>';
echo '<div id="search-result-area" class="mt-3">';
echo '</div>';
Modal::end();

