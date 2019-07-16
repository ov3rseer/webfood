<?php

use backend\controllers\report\TasksController;
use common\models\enum\ConsoleTaskStatus;

/* @var yii\web\View $this */
/* @var common\models\reference\ConsoleTask $model */

$this->title = 'Статус задачи №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/** @var TasksController $controller */
$controller = $this->context;

echo '<h3>Задача</h3>';
echo (string)$model;

echo '<h3>Состояние задачи</h3>';
echo (string)$model->status;

if ($model->result_text) {
    echo '<h3>Результат задачи</h3>';
    echo $model->result_text;
}

if (in_array($model->status_id, [ConsoleTaskStatus::PLANNED ,ConsoleTaskStatus::IN_PROGRESS])) {
    echo '<hr>';
    echo '<p>Данная страница будет обновляться автоматически до выполнения задачи.</p>';
    echo '<p>Сохраняейте спокойствие.</p>';
    $this->registerJs("setTimeout(function(){ window.location.reload(); }, 2000);");
}