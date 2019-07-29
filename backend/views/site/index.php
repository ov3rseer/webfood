<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Admin WebFood';

?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= Html::a('Импорт контрагентов и договоров',
                ['/system/import-contractor-and-contract/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= Html::a('Экспорт авторизационных данных новых контрагентов',
                ['export-contractors-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= Html::a('Экспорт предварительных заявок на следующую неделю',
                ['export-many-requests'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>

    </div>

</div>
