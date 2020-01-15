<?php


use backend\controllers\BackendModelController;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridView;
use backend\widgets\GridView\GridViewWithToolbar;
use yii\bootstrap\Html;
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

foreach ($requests as $key => $request) {
    $this->beginBlock('grid' . $key);
    echo GridView::widget([
        'id' => $gridWidgetId,
        'dataProvider' => new ActiveDataProvider(['query' => $request[1]]),
        'columns' => $columns,
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

$form = ActiveForm::begin(['action' => 'create']);

echo '<div class="row">';
echo '<div class="col-xs-12">';
echo Html::tag('h3', 'Создание заявок');
echo Html::tag('p', 'Для того, чтобы создать заявку, введите необходимую дату поставки и нажмите кнопку "Создать новую заявку".');
echo Html::tag('p', 'После создания вы можете найти заявку в списке "Новые" и добавить список необходимых продуктов.');
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-xs-3">';
echo $form->autoField($model, 'delivery_day');
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-xs-3">';
echo Html::submitButton('Создать новую заявку', ['id' => 'create-new-request', 'class' => 'btn btn-success']);
echo '</div>';
echo '</div>';
echo '<div class="row" style="margin-top:25px;">';
echo '<div class="col-xs-12">';
echo Tabs::widget([
    'items' => $tabs,
]);
echo '</div>';
echo '</div>';
$form->end();


