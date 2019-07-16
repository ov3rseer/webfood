<?php

use backend\controllers\reference\FileController;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\reference\Reference;

/* @var yii\web\View $this */
/* @var common\models\reference\File $model */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var common\models\reference\File $filterModel */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var FileController $controller */
$controller = $this->context;
/** @noinspection PhpUnhandledExceptionInspection */
$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = 'grid-' . $shortClassName;

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

$gridToolbarOptions = [
    'layout' => $toolbarLayout,
];

?>
<div class="document-index">
    
    <?= GridViewWithToolbar::widget([
        'id' => $gridWidgetId,
        'gridToolbarOptions' => $gridToolbarOptions,
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'columns' => function() use ($controller, $model, $filterModel) {
                return $controller->generateAutoColumns($model, $filterModel);
            },
            'actionColumn' => ['template' => '{update}{delete}'],
            'rowOptions' => function (Reference $model) {
                return $model->is_active ? [] : ['style' => 'background-color: #f2dede;'];
            },
        ],
    ]); ?>

</div>
