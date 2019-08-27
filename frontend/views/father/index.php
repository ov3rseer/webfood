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

    ?>

    <div class="container">
        <div class="col-lg-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span style="display: flex; justify-content: space-between;">
                        <span class="panel-title h2">Ваши дети</span>
                        <?= Html::a(' <span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span>',
                            '#', ['id' => $addChildButtonId, 'class' => 'text-success']) ?>
                    </span>
                </div>
                <?php
                if ($father->fatherChildren) {
                    echo '<div class="panel-body">';
                    foreach ($father->fatherChildren as $fatherChild) {
                        echo Html::encode($fatherChild->child);
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
    <?php

    $this->registerJs("
        $().ready(function() {

            var childSearchFluent = new FluentUI({
                '#" . $addChildButtonId . "' : {
                    'click' : function() {
                        $('#" . $addChildModalId . "').modal('show');
                    }
                },
                '#add-child-input' : {
                    'input' : function() {
                        let el = $(this);
                        let userInput = el.val();
                        $.ajax({
                            url: 'site/search-child',
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
                                    $('#add-child-input').val(child);                                   
                                    $('#add-child-input').attr('data-id', childId);
                                    $('#search-result-area').empty();                 
                                });
                            },
                        });
                    }
                }
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
    echo Html::textInput(null, null, ['id' => 'add-child-input', 'aria-describedby' => 'basic-addon2', 'class' => 'form-control', 'placeholder' => 'Введите ФИО ребёнка']);
    echo Html::beginTag('span', ['class' => 'input-group-btn']);
    echo Html::button('<span class="glyphicon glyphicon-ok"></span>', ['class' => 'btn btn-success']);
    echo Html::endTag('span');
    echo Html::endTag('div');
    echo Html::tag('div', null, ['id' => 'search-result-area', 'class' => 'mt-3']);
    Modal::end();
}
?>