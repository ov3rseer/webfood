<?php

/* @var $this yii\web\View */

use common\models\enum\UserType;
use common\models\reference\Father;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$this->title = 'WebFood';

?>
<div class="site-index">

    <div class="container">

        <?php
        $father = null;
        if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER) {
            /** @var Father $father */
            $father = Father::findOne(['user_id' => Yii::$app->user->id]);
        }

        if ($father) {
            $addChildButtonId = 'add-child-button';
            $deleteChildButtonId = 'delete-child-button';
            $addChildModalId = 'add-child-modal';
            $searchChildInputId = 'search-child-input';
            $addChildInputId = 'add-child-input';

            echo Html::beginTag('div', ['class' => 'container']);
            echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-4 col-lg-4']);
            echo Html::beginTag('div', ['class' => 'panel panel-default']);
            echo Html::beginTag('div', ['class' => 'panel-heading']);
            echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
            echo Html::tag('span', 'Ваши дети', ['class' => 'panel-title h2']);
            echo Html::a('<span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span>',
                '#', ['id' => $addChildButtonId, 'class' => 'text-success']);
            echo Html::endTag('span');
            echo Html::endTag('div'); // panel-heading

            if ($father->fatherChildren) {

                echo Html::beginTag('div', ['class' => 'list-group accordion', 'id' => 'accordionExample']);
                foreach ($father->fatherChildren as $fatherChild) {
                    echo Html::beginTag('div', ['class' => 'list-group-item list-group-item-action']);
                    echo Html::beginTag('div');
                    echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
                    echo Html::a($fatherChild->child->name_full, '#', [
                        'style' => 'text-decoration:none; border-bottom: 1px dashed #000080;',
                        'data-toggle' => 'collapse',
                        'data-target' => '#collapse' . $fatherChild->child_id,
                        'aria-expanded' => true,
                        'aria-controls' => 'collapse' . $fatherChild->child_id
                    ]);
                    echo Html::a('<span class="glyphicon glyphicon-minus"></span><span class="glyphicon glyphicon-user"></span>',
                        '#', ['class' => $deleteChildButtonId . ' text-danger', 'data' => ['child-id' => $fatherChild->child_id]]);
                    echo Html::endTag('span');
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['id' => 'collapse' . $fatherChild->child_id, 'class' => 'collapse', 'data-parent' => '#accordionExample']);
                    if ($card = $fatherChild->child->card) {

                        echo Html::beginTag('p', ['style' => 'margin-top:10px; ']);
                        echo Html::tag('span', '<em class="text-muted">Номер карты:</em> ' . Html::encode($card->card_number));
                        echo Html::endTag('p');
                        echo Html::beginTag('p', ['style' => 'display:flex; justify-content: space-between;']);
                        echo Html::tag('span', '<em class="text-muted">Баланс:</em> ' . Html::encode($card->balance));
                        echo Html::a('Пополнить', '#', ['class' => 'btn btn-success']);
                        echo Html::endTag('p');
                    }
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                }
                echo Html::endTag('div');

            }
            echo Html::endTag('div'); // panel panel-default
            echo Html::endTag('div'); // col-xs-4
            echo Html::endTag('div'); // container


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
                                            var childId = $(this).data('child-id');                                 
                                            $('#" . $searchChildInputId . "').val(child);                                   
                                            $('#" . $searchChildInputId . "').attr('data-child-id', childId);
                                            $('#search-result-area').empty();                 
                                        });
                                    },
                                });
                            }
                        },
                        '#" . $addChildInputId . "' : {
                            'click' : function() {           
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
                            }  
                        }, 
                        '." . $deleteChildButtonId . "' : {
                            'click' : function(e) {          
                                e.stopPropagation();
                                e.preventDefault();
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
            ?>
            <div class="input-group">
                <?= Html::textInput(null, null, [
                    'id' => $searchChildInputId,
                    'aria-describedby' => 'basic-addon2',
                    'class' => 'form-control',
                    'placeholder' => 'Введите ФИО ребёнка'
                ]); ?>
                <span class="input-group-btn">
                <?= Html::button('<span class="glyphicon glyphicon-ok"></span>', [
                    'id' => $addChildInputId,
                    'class' => 'btn btn-success'
                ]); ?>
                </span>
            </div>
            <div id="search-result-area" class="mt-3">
            </div>

            <?php
            Modal::end();
        }
        ?>
    </div>

</div>
