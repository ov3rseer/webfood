<?php

/* @var $this yii\web\View */

$this->title = 'WebFood';
?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= \yii\helpers\Html::a('Предварительная заявка', ['request/preliminary-request/index'], ['class' => 'btn btn-lg btn-success', 'style' =>'width:300px;']) ?></p>
        <p><?= \yii\helpers\Html::a('Корректировка заявки', ['request/correction-request/index'], ['class' => 'btn btn-lg btn-success', 'style' =>'width:300px;']) ?></p>

    </div>

</div>
