<?php

use backend\actions\base\UpdateAction;
use backend\controllers\document\DocumentController;
use backend\controllers\report\DocumentDependenceStructureController;
use backend\widgets\ActiveField;
use common\models\document\Document;
use common\models\system\Entity;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\document\Document $model */
/* @var string|null $activeTabRelation */

/** @var DocumentController $controller */
$controller = $this->context;

/** @var UpdateAction $action */
$action = $controller->action;

$this->title = (string)$model;
$this->params['breadcrumbs'][] = ['label' => $model->getPluralName(), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Просмотр';

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
    'encodeErrorSummary' => false,
]);

$buttons = [];
$createRelatedItems = [];
if ($model instanceof Document && Yii::$app->user->can(DocumentDependenceStructureController::className() . '.Index')) {
    $buttons[] = Html::a(
        'Связанные документы',
        [
            '/report/document-dependence-structure/index',
            'DocumentDependenceStructure[document_basis_id]' => $model->id,
            'DocumentDependenceStructure[document_basis_type_id]' => Entity::getIdByClassName($model::className()),
        ],
        ['class' => 'btn btn-info iframe-open', 'target' => '_blank']
    );
}

$classesForRelatedDocuments = array_keys($model->getSettingsForRelatedDocuments());
foreach ($classesForRelatedDocuments as $class) {
    /** @var Document $class */
    /** @var Document $doc */
    $doc = new $class();
    $createRelatedItems[] = [
        'label' => $doc->getSingularName(),
        'url' => $class::getCreateUrl([
            'basis_id' => $model->id,
            'basis_type_id' => Entity::getIdByClassName($model::className()),
        ]),
        'linkOptions' => [
            'class' => 'iframe-open',
        ],
    ];
}
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="btn-toolbar">
                <?php
                /** @noinspection PhpUnhandledExceptionInspection */
                echo ButtonGroup::widget(['buttons' => $buttons]);
                if ($createRelatedItems) {
                    $dropdown = '<a href="#" data-toggle="dropdown" class="btn btn-info dropdown-toggle">Создать на основании <b class="caret"></b></a>';
                    /** @noinspection PhpUnhandledExceptionInspection */
                    $dropdown .= Dropdown::widget(['items' => $createRelatedItems]);
                    /** @noinspection PhpUnhandledExceptionInspection */
                    echo ButtonGroup::widget(['buttons' => [$dropdown]]);
                }
                ?>
            </div>
        </div>
    </div>
<?php

echo $form->errorSummary($model);

$fieldsOptions = $model->getFieldsOptions();
foreach ($fieldsOptions as $field => $fieldOptions) {
    if (in_array($field, ['create_user_id', 'create_date', 'date', 'update_user_id', 'update_date', 'document_basis_id', 'document_basis_type_id', 'comment'])) {
        continue;
    }
    if (in_array($fieldOptions['type'], [ActiveField::TIMESTAMP, ActiveField::DATETIME, ActiveField::DATE])) {
        $fieldOptions['displayType'] = ActiveField::STRING;
    }
    $fieldObject = $form->autoField($model, $field, $fieldOptions);
    if ($fieldObject) {
        echo $fieldObject->readonly();
    }
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
        'content' => isset($tab['viewView']) ? $controller->renderPartial($tab['viewView'], $tabParams) : $tab['content'],
    ];
    if (!empty($activeTabRelation) && ($activeTabRelation == $relation)) {
        $tab['active'] = true;
    }
    $tabs[] = $tab;
}

$this->beginBlock('other-stuff');
echo '<div class="row">';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->autoField($model, 'create_user_id');
echo '</div>';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->autoField($model, 'update_user_id');
echo '</div>';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->autoField($model, 'document_basis_type_id', $fieldsOptions['document_basis_type_id']);
echo $form->autoField($model, 'document_basis_id', $fieldsOptions['document_basis_id']);
echo '</div>';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->autoField($model, 'comment', $fieldsOptions['comment']);
echo '</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->autoField($model, 'date');
echo '</div>';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->field($model, 'create_date')->textInput(['readonly' => true]);
echo '</div>';
echo '<div class="col-xs-12 col-sm-6 col-md-3">';
echo $form->field($model, 'update_date')->textInput(['readonly' => true]);
echo '</div>';
echo '</div>';


$this->endBlock();

$tabs[] = [
    'label' => 'Дополнительно',
    'content' => $this->blocks['other-stuff'],
];

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
