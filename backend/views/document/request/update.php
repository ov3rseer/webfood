<?php

use backend\actions\base\UpdateAction;
use backend\controllers\document\RequestController;
use backend\controllers\report\DocumentDependenceStructureController;
use backend\widgets\ActiveField;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;
use common\models\system\Entity;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Tabs;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\widgets\ActiveForm;
use yii\web\View;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\document\Request $model */
/* @var string|null $activeTabRelation */
/* @var array $registerErrors */

/** @var RequestController $controller */
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
$reflection = new ReflectionClass($model->className());
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

$buttons = [
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']),
];
$createRelatedItems = [];
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
    if ($model instanceof Document) {
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
    }
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
                 $exportButtons = [];
                if (!$model->isNewRecord && in_array($model->status_id,[DocumentStatus::DRAFT, DocumentStatus::POSTED])) {
                    $exportButtons[] = Html::a('Выгрузить в XML', ['export-request', 'id' => $model->id],
                        ['class' => 'btn btn-info', 'target' => '_blank']
                    );
                }
                /** @noinspection PhpUnhandledExceptionInspection */
                echo ButtonGroup::widget(['buttons' => $exportButtons]);

                ?>
            </div>
        </div>
    </div>
<?php

echo $form->errorSummary($model);

$this->registerJs("function showRegistersErrors(){ $('.registers-errors').modal(); }", View::POS_END);

echo Html::beginTag('div', ['class' => 'registers-errors modal fade', 'style' => 'display:none;', 'tabindex' => -1, 'role' => 'dialog']);
echo Html::beginTag('div', ['class' => 'modal-dialog', 'role' => 'document', 'style' => 'width:80%;']);
echo Html::beginTag('div', ['class' => 'modal-content']);
echo Html::beginTag('div', ['class' => 'modal-header']);
echo Html::button('×', ['encode' => false, 'class' => 'close', 'data-dismiss' => 'modal']);
echo Html::tag('div', 'Ошибки проведения документа по регистрам', ['class' => 'modal-title']);
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'modal-body']);
$settingsForDependentRegisters = $model->getSettingsForDependentRegisters();
foreach ($registerErrors as $registerClass => $data) {
    echo Html::tag('h3', $data['label']);
    $balanceError = call_user_func($settingsForDependentRegisters[$registerClass]['balance_error']);
    if ($balanceError) {
        echo Html::tag('div', '<strong>Возможные причины ошибки:</strong> ' . $balanceError, ['class' => 'alert alert-info']);
    }
    /** @noinspection PhpUnhandledExceptionInspection */
    echo GridView::widget([
        'dataProvider' => $data['dataProvider'],
        'columns' => $data['columns'],
        'layout' => '{items}',
    ]);
    echo '<br>';
}
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');

$fieldsOptions = $model->getFieldsOptions();
foreach ($fieldsOptions as $field => $fieldOptions) {
    if (in_array($field, ['create_user_id', 'create_date', 'update_user_id', 'update_date', 'document_basis_id', 'comment'])) {
        continue;
    }
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
echo '</div>';

echo '<div class="row">';
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
