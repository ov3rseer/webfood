<?php

/* @var $this yii\web\View */

$this->title = 'Admin WebFood';
?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= \yii\helpers\Html::a('Импорт контрагентов и договоров',
                ['/system/import-contractor-and-contract/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:520px;']) ?></p>
        <p><?= \yii\helpers\Html::a('Экспорт авторизационных данных контрагентов',
                ['export-contractors-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:520px;']) ?></p>


    </div>

</div>
