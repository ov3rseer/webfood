<?php

/* @var $this yii\web\View */

$this->title = 'WebFood';
?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= \yii\helpers\Html::a('Предварительная заявка', ['form/preliminary-request-form/index'], ['class' => 'btn btn-lg btn-success', 'style' =>'width:300px;']) ?></p>
        <p><?= \yii\helpers\Html::a('Корректировка заявки', ['form/correction-request-form/index'], ['class' => 'btn btn-lg btn-success', 'style' =>'width:300px;']) ?></p>

    </div>

</div>
