<?php

use backend\controllers\reference\ReferenceController;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\reference\Reference;

/* @var yii\web\View $this */
/* @var Reference $model */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var Reference $filterModel */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var ReferenceController $controller */
$controller = $this->context;

/** @noinspection PhpUnhandledExceptionInspection */
$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = 'grid-' . $shortClassName;

$gridOptions = [
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => function() use ($controller, $model, $filterModel) {
        return $controller->generateAutoColumns($model, $filterModel);
    },
    'rowOptions' => function (Reference $model) {
        return $model->is_active ? [] : ['style' => 'background-color: #f2dede;'];
    },
];

$toolbarLayout = [['refresh']];
if (Yii::$app->user->can($controller::className() . '.Create')) {
    $toolbarLayout[0][] = 'create';
}
if (Yii::$app->user->can($controller::className() . '.Delete')) {
    $toolbarLayout[0][] = 'delete';
} else {
    $gridOptions['checkboxColumn'] = false;
}
$toolbarLayout[][] = 'columns';

?>
<div class="reference-index">
    
    <?= GridViewWithToolbar::widget([
        'id' => $gridWidgetId,
        'gridToolbarOptions' => [
            'layout' => $toolbarLayout,
        ],
        'gridOptions' => $gridOptions,
    ]); ?>

</div>
