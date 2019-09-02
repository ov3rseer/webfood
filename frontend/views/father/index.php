<?php

/* @var $this yii\web\View */

$this->title = 'WebFood';

use common\models\enum\UserType;
use common\models\reference\Father;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$father = null;
if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER) {
    /** @var Father $father */
    $father = Father::findOne(['user_id' => Yii::$app->user->id]);
}

if ($father) {
    $addChildButtonId = 'add-child-button';
    $addChildModalId = 'add-child-modal';
    $searchChildInputId = 'search-child-input';
    $addChildInputId = 'add-child-input';

    echo Html::beginTag('div', ['class' => 'container']);
    echo Html::beginTag('div', ['class' => 'col-lg-3']);
    echo Html::beginTag('div', ['class' => 'panel panel-default']);
    echo Html::beginTag('div', ['class' => 'panel-heading']);
    echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
    echo Html::tag('span', 'Ваши дети', ['class' => 'panel-title h2']);
    echo Html::a(' <span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span>',
        '#', ['id' => $addChildButtonId, 'class' => 'text-success']);
    echo Html::endTag('span');
    echo Html::endTag('div');
    if ($father->fatherChildren) {
        echo Html::beginTag('div', ['class' => 'panel-body']);
        foreach ($father->fatherChildren as $fatherChild) {
            echo Html::encode($fatherChild->child->name).'<br>';
        }
        echo Html::endTag('div');
    }
    echo Html::endTag('div');
    echo Html::endTag('div');
    echo Html::endTag('div');


    $this->registerJs("
        $().ready(function() {
            var childSearchFluent = new FluentUI({
                '#" . $addChildButtonId . "' : {
                    'click' : function() {
                        $('#" . $addChildModalId . "').modal('show');
                    }
                },
                '#" . $searchChildInputId . "' : {
                    'input' : function() {                       
                        var userInput = $(this).val();
                        $.ajax({
                            url: 'father/my-child/search-child',
                            data: {
                                'userInput' : userInput
                            },
                            dataType: 'html',
                            type: 'POST',                           
                            success: function(data) {
                                $('#search-result-area').html(data);
                                $('#search-result-area .list-group-item').on('click', function() {                             
                                    var child = $(this).text();                                   
                                    var childId = $(this).data('id');                                 
                                    $('#" . $searchChildInputId . "').val(child);                                   
                                    $('#" . $searchChildInputId . "').attr('data-id', childId);
                                    $('#search-result-area').empty();                 
                                });
                            },
                        });
                    }
                },
                '#" . $addChildInputId . "' : {
                    'click' : function() {           
                        var childId = $('#" . $searchChildInputId . "').data('id');
                        $.ajax({
                            url: 'father/my-child/add-child',
                            data: {
                                'childId' : childId
                            },
                            dataType: 'html',
                            type: 'POST', 
                            success: function () {
                                location.reload();
                            }                       
                        });
                    }  
                },              
            });
        });
    ");

    Modal::begin([
        'header' => '<h2>Выберите своего ребенка</h2>',
        'options' => [
            'id' => $addChildModalId
        ]
    ]);
    echo Html::beginTag('div', ['class' => 'input-group']);
    echo Html::textInput(null, null, ['id' => $searchChildInputId, 'aria-describedby' => 'basic-addon2', 'class' => 'form-control', 'placeholder' => 'Введите ФИО ребёнка']);
    echo Html::beginTag('span', ['class' => 'input-group-btn']);
    echo Html::button('<span class="glyphicon glyphicon-ok"></span>', ['id' => $addChildInputId, 'class' => 'btn btn-success']);
    echo Html::endTag('span');
    echo Html::endTag('div');
    echo Html::tag('div', null, ['id' => 'search-result-area', 'class' => 'mt-3']);
    Modal::end();
}
