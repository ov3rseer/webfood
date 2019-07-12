<?php

use backend\actions\base\UpdateAction;
use backend\controllers\ModelController;
use backend\widgets\ActiveField;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\ActiveRecord $model */
/* @var string|null $activeTabRelation */

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

/** @noinspection PhpUnhandledExceptionInspection */
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

$buttons = [
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']),
];
if (!$model->isNewRecord) {
    if ((!($model instanceof Reference) && !($model instanceof Document))
        || (
            Yii::$app->user->can($controller::className() . '.Delete')
            && (
                $model instanceof Reference && $model->is_active
                || $model instanceof Document && $model->status_id != DocumentStatus::DELETED
            )
        )
    ){
        $buttons[] = Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить этот объект?',
                'method' => 'post',
            ],
        ]);
    }
    if ((!($model instanceof Reference) && !($model instanceof Document))
        || (
            Yii::$app->user->can($controller::className() . '.Restore')
            && (
                $model instanceof Reference && !$model->is_active
                || $model instanceof Document && $model->status_id == DocumentStatus::DELETED
            )
        )
    ){
        $buttons[] = Html::a('Восстановить', ['restore', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Вы действительно хотите восстановить этот объект?',
                'method' => 'post',
            ],
        ]);
    }
}
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="btn-toolbar">
                <?php
                /** @noinspection PhpUnhandledExceptionInspection */
                echo ButtonGroup::widget(['buttons' => $buttons]);
                ?>
            </div>
        </div>
    </div>
<?php

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
}

$tabs = [];
foreach ($controller->getTabs($model) as $relation => $tab) {
    $tabParams = isset($tab['params']) ? $tab['params'] : [];
    $tabParams['model'] = $model;
    $tabParams['form'] = $form;
    $tabParams['pjax'] = $pjax;
    $tab = [
        'label' => $tab['label'],
        'content' => isset($tab['viewUpdate']) ? $controller->renderPartial($tab['viewUpdate'], $tabParams) : $tab['content'],
    ];
    if (!empty($activeTabRelation) && ($activeTabRelation == $relation)) {
        $tab['active'] = true;
    }
    $tabs[] = $tab;
}

if ($tabs) {
    /** @noinspection PhpUnhandledExceptionInspection */
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
