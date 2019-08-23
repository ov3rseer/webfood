<?php

/* @var $this yii\web\View */

$this->title = 'WebFood';

use common\models\enum\UserType;
use common\models\reference\Father;
use kartik\widgets\Typeahead;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;

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
        $('#" . $addChildButtonId . "').click(function(e){ 
            $('#" . $addChildModalId . "').modal('show');
        });
    ");

    Modal::begin([
        'header' => '<h2>Выберите своего ребенка</h2>',
        'options' => [
            'id' => $addChildModalId
        ]
    ]);
    Pjax::begin();

    echo Typeahead::widget([
        'name' => 'child',
        'pluginOptions' => [
            'highlight' => true,
            'minLength' => 3,
        ],
        'options' => [
            'placeholder' => 'Введите ФИО ребенка',
            'class' => 'form-control'
        ],
        'dataset' => [
            [
                'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                'display' => 'value',
                'remote' => [
                    'url' => Url::to(['site/search-child']) . '?userInput=%QUERY',
                    'wildcard' => '%QUERY',
                ],
                'templates' => [
                    'notFound' => '<div class="text-danger" style="padding:0 8px">Ничего не найдено.</div>',
                ]
            ]
        ],
    ]);

    Pjax::end();
    Modal::end();
}
?>