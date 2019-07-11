<?php

use backend\controllers\system\RoleController;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\system\Role $model */

/** @var RoleController $controller */
$controller = $this->context;

/** @var \yii\base\Action $action */
$action = $controller->action;

if ($model->isNewRecord) {
    $this->title = $model->getSingularName() . ' (новый)';
    $this->params['breadcrumbs'][] = ['label' => $model->getPluralName(), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = (string)$model;
    $this->params['breadcrumbs'][] = ['label' => $model->getPluralName(), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => (string)$model, 'url' => ['update', 'id' => $model->name]];
    $this->params['breadcrumbs'][] = 'Изменение';
}

$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();

$pjaxId = $shortClassName . '-pjax';
$formId = $shortClassName . '-form';

$pjax = Pjax::begin([
    'id' => $pjaxId,
    'linkSelector' => '#' . $pjaxId . ' a[data-pjax=1]',
]);

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
]);

echo $form->errorSummary($model);

foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
    echo $form->autoField($model, $field, $fieldOptions);
}

$tabs = [];
foreach ($controller->getTabs($model) as $tab) {
    $tabParams = isset($tab['params']) ? $tab['params'] : [];
    $tabParams['model'] = $model;
    $tabParams['form'] = $form;
    $tabParams['pjax'] = $pjax;
    $tabs[] = [
        'label' => $tab['label'],
        'content' => isset($tab['view']) ? $controller->renderPartial($tab['view'], $tabParams) : $tab['content'],
    ];
}

if ($tabs) {
    echo Tabs::widget(['items' => $tabs]);
    $this->registerJs('
        $(document).on("click", "input[name=\'' . Html::getInputName($model, 'assigned_all[]') . '\']", function (e) {
            var $this = $(this);
            var $class = $this.prop("class").replace("-all", "");
            $("." + $class).prop("checked", $this.prop("checked"));
        });
    ');
}

$buttons = [
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']),
];
if (!$model->isNewRecord) {
    $buttons[] = Html::a('Удалить', ['delete', 'id' => $model->name], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Вы действительно хотите удалить этот объект?',
            'method' => 'post',
        ],
    ]);
}
echo ButtonGroup::widget(['buttons' => $buttons]);

$form->end();

$pjax->end();
