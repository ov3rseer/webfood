<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Admin WebFood';

?>
<div class="site-index">

    <div class="jumbotron">
        <div class="row mb-4">
            <div class="col-xs-6">
                <?= Html::a('Импорт поставщиков продуктов',
                    ['/system/import-product-provider/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?>
            </div>
            <div class="col-xs-6">
                <?= Html::a('Импорт объектов обслуживания',
                    ['/system/import-service-object/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xs-6">
                <?= Html::a('Экспорт авторизационных данных новых поставщиков',
                    ['export-provider-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?>
            </div>
            <div class="col-xs-6">
                <?= Html::a('Экспорт авторизационных данных новых объектов',
                    ['export-object-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
            </div>
            <div class="col-xs-6">
                <?= Html::a('Экспорт предварительных заявок на следующую неделю',
                    ['export-many-requests'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?>
            </div>
        </div>
    </div>
</div>


