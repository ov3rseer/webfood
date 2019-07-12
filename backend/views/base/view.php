<?php

use backend\actions\base\UpdateAction;
use backend\controllers\ModelController;
use backend\widgets\ActiveField;
use common\models\reference\Reference;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\ActiveRecord $model */

/** @var ModelController $controller */
$controller = $this->context;

/** @var UpdateAction $action */
$action = $controller->action;

if ($model->isNewRecord) {
    $this->title = $model->getSingularName() . ' (новый)';
    $this->params['breadcrumbs'][] = ['label' => $model->getPluralName(), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = (string)$model;
    $this->params['breadcrumbs'][] = ['label' => $model->getPluralName(), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => (string)$model, 'url' => ['update', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = 'Изменение';
}

$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();

$this->beginBlock('main');

$pjaxId = $shortClassName . '-pjax';
$formId = $shortClassName . '-form';

$pjax = Pjax::begin([
    'id' => $pjaxId,
    'linkSelector' => '#' . $pjaxId . ' a[data-pjax=1]',
]);

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'options' => ['enctype' => 'multipart/form-data'],
]);

echo $form->errorSummary($model);

foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
    echo $form->autoField($model, $field, $fieldOptions);
    if ($fieldOptions['type'] == ActiveField::FILE && isset($fieldOptions['options']['related_field'])) {
        $fileId = $model->{$fieldOptions['options']['related_field']};
        if ($fileId) {
            echo '<div class="form-group">' . Html::a('Скачать файл', ['/reference/file/download', 'id' => $fileId],
                ['target' => '_blank', 'data-pjax' => 0]) . '</div>';
        }
    }
    if ($model instanceof Reference && $field == 'name') {
        echo $form->field($model, 'is_active')->readonly();
    }
}

$tabs = [];
foreach ($controller->getTabs($model) as $tab) {
    $tabParams = isset($tab['params']) ? $tab['params'] : [];
    $tabParams['model'] = $model;
    $tabParams['form'] = $form;
    $tabParams['pjax'] = $pjax;
    $tabs[] = [
        'label' => $tab['label'],
        'content' => isset($tab['viewView']) ? $controller->renderPartial($tab['viewView'], $tabParams) : $tab['content'],
    ];
}

if ($tabs) {
    echo Tabs::widget(['items' => $tabs]);
}
$form->end();

$pjax->end();

$this->endBlock();

if ($action->tabsViewPath) {
    echo $controller->renderPartial($action->tabsViewPath, [
        'model' => $model,
        'tabKey' => 'main',
        'tabContent' => $this->blocks['main'],
    ]);
} else {
    echo $this->blocks['main'];
}
