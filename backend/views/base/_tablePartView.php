<?php

use backend\controllers\ModelController;
use backend\widgets\GridView\GridViewWithToolbar;

/* @var yii\web\View $this */
/* @var common\models\ActiveRecord $model */
/* @var backend\widgets\ActiveForm $form */
/* @var yii\widgets\Pjax $pjax */
/* @var string $relation */
/* @var string $relationClass */

/** @var ModelController $controller */
$controller = $this->context;

$formId = $form->id;
$pjaxId = $pjax->id;

/** @noinspection PhpUnhandledExceptionInspection */
$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = $shortClassName . '-' . $relation;

/** @noinspection PhpUnhandledExceptionInspection */
echo GridViewWithToolbar::widget([
    'id' => $gridWidgetId,
    'gridPjaxOptions' => false,
    'gridToolbarOptions' => false,
    'gridOptions' => [
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->{$relation},
            'pagination' => false,
            'key' => 'id',
        ]),
        'layout' => '{items}',
        'showFooter' => true,
        'actionColumn' => false,
        'checkboxColumn' => false,
        'columns' => $controller::getTablePartColumns($model, $relation, $form, true),
    ],
]);
