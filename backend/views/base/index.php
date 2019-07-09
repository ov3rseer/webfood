<?php

use backend\controllers\ModelController;
use backend\widgets\GridView\GridViewWithToolbar;

/* @var yii\web\View $this */
/* @var common\models\ActiveRecord $model */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var common\models\ActiveRecord $filterModel */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var ModelController $controller */
$controller = $this->context;

$gridOptions = [
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'columns' => function() use ($controller, $model, $filterModel) {
        return $controller->generateAutoColumns($model, $filterModel);
    },
];

/** @noinspection PhpUnhandledExceptionInspection */
$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = 'grid-' . $shortClassName;
$toolbarLayout = [['refresh','create','delete']];

?>
<div class="document-index">

    <?= GridViewWithToolbar::widget([
        'id' => $gridWidgetId,
        'gridToolbarOptions' => [
            'layout' => $toolbarLayout,
        ],
        'gridOptions' => $gridOptions,
    ]); ?>

</div>
