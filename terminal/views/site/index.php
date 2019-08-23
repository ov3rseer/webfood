<?php

/* @var $this yii\web\View */

/* @var $model \terminal\models\TerminalForm */

use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

$this->title = 'WebFood';

$balanceModalId = 'balance-modal';

$this->registerJs("
$().ready(function() {

    var loginFluent = new FluentUI({
        '#check-balance' : {
            'click' : function() {
                $('#update-balance').click();
                $('#$balanceModalId').modal('show');
            }
        }
    });

});
");

?>

<div class="site-index">

    <div class="jumbotron">
        <div class="my-3" style="display: flex; justify-content: center;">
            <?=
            Html::button('Проверить баланс', [
                'class' => 'btn btn-lg btn-success col-xs-6',
                'id' => 'check-balance',
            ])
            ?>
        </div>
        <div class="my-3" style="display: flex; justify-content: center;">
            <?=
            Html::button('Меню', [
                'class' => 'btn btn-lg btn-success col-xs-6',
                'id' => 'menu',
                'onclick' => 'location.href = "menu"'
            ])
            ?>
        </div>
        <?= \yii\helpers\Html::beginForm(['/site/logout'], 'post', ['class' => 'my-3', 'style' => 'display: flex; justify-content: center;']) ?>
        <?=
        Html::submitButton('Выход', [
            'class' => 'btn btn-lg btn-success col-xs-6',
        ])
        ?>
        <?= \yii\helpers\Html::endForm() ?>
    </div>

    <?php
    Modal::begin([
        'options' => [
            'id' => $balanceModalId,
        ],
        'header' => 'Ваш баланс',
    ]);
    ?>
    <?php Pjax::begin(); ?>
    <?= Html::a("", ['site/index'], ['class' => 'd-none', 'id' => 'update-balance']) ?>
    <h1 class="display-1 text-center"><?= Yii::$app->user->identity->balance ?><span class="glyphicon glyphicon-ruble lead text-muted"></span></h1>
    <?php Pjax::end(); ?>
    <?php
    Modal::end();
    ?>

</div>
