<?php

use backend\controllers\BackendModelController;
use backend\widgets\GridView\GridView;
use backend\widgets\IframeDialog\IframeDialogAsset;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\ActiveRecord $model */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var common\models\ActiveRecord $filterModel */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var BackendModelController $controller */
$controller = $this->context;

echo Html::beginTag('div', ['class' => 'reference-select']);

$gridId = 'reference-select-grid';
$pjaxId = 'reference-select-pjax';

//echo GridViewToolbar::widget([
//    'gridId' => $gridId,
//    'gridPjaxId' => $pjaxId,
//    'layout' => ['main' => ['refresh', 'create']],
//    'tokens' => [
//        'create' => function() {
//            return Html::a('<i class="glyphicon glyphicon-plus"></i>',
//                ['create', 'layout' => 'iframe', 'redirect' => Url::to(['select', 'layout' => 'iframe'])],
//                ['class' => 'btn btn-success']
//            );
//        },
//    ],
//]);

$pjaxGridWidget = Pjax::begin(['id' => $pjaxId,]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => $controller->generateAutoColumns($model, $filterModel),
    'actionColumn'   => [
        'template' => '{apply}',
        'buttons'  => [
            'apply' => function ($url, $model) {
                unset($url);
                return Html::a('<span class="glyphicon glyphicon-ok"></span>','#', [
                    'data-key' => $model->primaryKey,
                    'data-name' => $model,
                    'class' => 'apply',
                ]);
            },
        ]
    ],
]);

IframeDialogAsset::register($this);
$this->registerJs("
    $('.apply').click(function (e) {
        var t = $(this).ownerDialog().iframedialog('option', 'opener');
        t = t.getInOwnContext();
        t.find('.reference-field').select2Extend('updateItems', {
            items: [{id: $(this).data('key'), text: $(this).data('name')}],
            selected: [$(this).data('key')]
        });
        $(this).ownerDialog().iframedialog('hide');
        return false;
    });
");

$pjaxGridWidget->end();

echo Html::endTag('div');
