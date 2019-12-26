<?php


use backend\controllers\BackendModelController;
use backend\widgets\GridView\GridViewWithToolbar;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;

/* @var yii\web\View $this */
/* @var frontend\models\serviceObject\RequestForm $model */
/* @var yii\data\ActiveDataProvider $requests */
/* @var array $columns */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var BackendModelController $controller */
$controller = $this->context;

/** @noinspection PhpUnhandledExceptionInspection */
$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = 'grid-' . $shortClassName;
$toolbarLayout = [['refresh', 'create']];

foreach ($requests as $key => $request) {
    $this->beginBlock('grid' .$key);
    echo GridViewWithToolbar::widget([
        'id' => $gridWidgetId,
        'gridToolbarOptions' => [
            'layout' => $toolbarLayout,
        ],
        'gridOptions' => [
            'dataProvider' => new ActiveDataProvider(['query' => $request[1]]),
            'columns' => $columns,
        ],
    ]);
    $this->endBlock();
}

$tabs = [];
foreach ($requests as $key => $request) {
    $tabs[] = [
        'label' => $request[0],
        'content' => $this->blocks['grid' . $key],
    ];;
}

echo Tabs::widget([
    'items' => $tabs,
]);