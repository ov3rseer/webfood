<?php

use backend\widgets\GridView\GridView;

/* @var yii\web\View $this */
/* @var backend\widgets\ActiveForm $form */
/* @var yii\widgets\Pjax $pjax */
/* @var \yii\data\ArrayDataProvider $dataProvider */
/* @var string $section */
/* @var array $columns */

$formId = $form->id;
$pjaxId = $pjax->id;

$gridId = $section . '-grid';

echo GridView::widget([
    'id'             => $gridId,
    'dataProvider'   => $dataProvider,
    'layout'         => '{items}',
    'actionColumn'   => false,
    'checkboxColumn' => false,
    'columns'        => $columns,
]);
