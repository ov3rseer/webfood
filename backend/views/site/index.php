<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Admin WebFood';

?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= Html::a('Импорт поставщиков продуктов',
                ['/system/import-product-provider/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= Html::a('Импорт объектов обслуживания',
                ['/system/import-service-object/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= Html::a('Экспорт авторизационных данных новых объектов',
                ['export-service-object-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= Html::a('Экспорт предварительных заявок на следующую неделю',
                ['export-many-requests'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>

    </div>

</div>

