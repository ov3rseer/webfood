<?php

use backend\widgets\GridView\GridViewWithToolbar;
use frontend\controllers\FrontendModelController;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var common\models\ActiveRecord $model */
/* @var backend\widgets\ActiveForm $form */
/* @var yii\widgets\Pjax $pjax */
/* @var string $relation */
/* @var string $relationClass */

/** @var FrontendModelController $controller */
$controller = $this->context;

$formId = $form->id;
$pjaxId = $pjax->id;

/** @noinspection PhpUnhandledExceptionInspection */
$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = $shortClassName . '-' . $relation;

$allRules = $model->getRulesForProcessingTableParts();
$rules = isset($allRules[$relation]) ? $allRules[$relation] : [];
$rulesButtons = [];
foreach ($rules as $ruleKey => $rule) {
    $rulesButtons[] = [
        'label' => $rule['name'],
        'url' => '#',
        'linkOptions' => [
            'class' => 'process-table-part',
            'data-rule' => $ruleKey,
            'data-pjax' => isset($rule['is_pjax']) && !$rule['is_pjax'] ? 0 : 1,
        ],
    ];
}

$toolbarLayout = [['add', 'delete']];
if ($rulesButtons) {
    $toolbarLayout[] = ['process'];
    $this->registerJs("
        $('.process-table-part[data-pjax=1]').click(function(e){
            e.preventDefault();
            $.pjax.reload('#" . $pjaxId . "', {
                url: $('#" . $formId . "').attr('action'),
                replace: false,
                timeout: 5000,
                type: 'POST',
                data: $('#" . $formId . "').serialize() + '&processTablePart=" . $relation . "&rule=' + $(this).data('rule'),
            });
        });
    ");
}

/** @noinspection PhpUnhandledExceptionInspection */
echo GridViewWithToolbar::widget([
    'id' => $gridWidgetId,
    'gridPjaxOptions' => false,
    'gridToolbarOptions' => [
        'layout' => $toolbarLayout,
        'tokens' => [
            'add' => function() use ($pjaxId, $formId, $relation) {
                $buttonId = 'add_new_row_' . $relation;
                $this->registerJs("
                    $('#" . $buttonId . "').click(function(e){
                        e.preventDefault();
                        $.pjax.reload('#" . $pjaxId . "', {
                            url: $('#" . $formId . "').attr('action'),
                            replace: false,
                            timeout: 5000,
                            type: 'POST',
                            data: $('#" . $formId . "').serialize() + '&addTablePartRow=" . $relation . "',
                        });
                    });
                ");
                return Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить', '#', [
                    'id' => $buttonId,
                    'class' => 'btn btn-success',
                    'title' => 'Добавить новую строку',
                ]);
            },
            'delete' => function() use ($gridWidgetId, $pjaxId, $formId, $relation) {
                $buttonId = 'delete_rows_' . $relation;
                $this->registerJs("
                    $('#" . $buttonId . "').click(function(e){
                        e.preventDefault();
                        var checkedItems = $('#" . $gridWidgetId . "-grid input[name^=selection]:checked:visible');
                        if (!checkedItems.length) {
                            alert('Не выбраны строки для удаления');
                            return;
                        }
                        if (!confirm('Вы действительно хотите удалить выделенные элементы?')) {
                            return;
                        }                       
                        checkedItems.each(function(e, item){
                            $(item).closest('tr').remove();
                        });
                        $.pjax.reload('#" . $pjaxId . "', {
                            url: $('#" . $formId . "').attr('action'),
                            replace: false,
                            timeout: 5000,
                            type: 'POST',
                            data: $('#" . $formId . "').serialize() + '&deleteTablePartRow=" . $relation . "'
                        });
                    });
                ");
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                    'id' => $buttonId,
                    'class' => 'btn btn-danger',
                    'title' => 'Удалить отмеченные строки',
                ]);
            },
            'process' => function() use ($rulesButtons) {
                return ButtonDropdown::widget([
                    'label' => 'Заполнить',
                    'dropdown' => [
                        'items' => $rulesButtons,
                    ],
                    'options' => [
                        'class' => 'btn btn-warning',
                    ],
                ]);
            },
        ],
    ],
    'gridOptions' => [
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $model->{$relation},
            'pagination' => false,
            'key' => 'id',
        ]),
        'layout' => '{items}',
        'showFooter' => true,
        'actionColumn' => [
            'template' => '{delete}',
            'headerOptions' => ['style' => 'width:30px;'],
            'buttons' => [
                'delete' => function() use ($gridWidgetId, $pjaxId, $formId, $relation) {
                    $this->registerJs("
                        $('#" . $gridWidgetId . "-grid .delete-row').click(function(e){
                            e.preventDefault();
                            if (!confirm('Вы действительно хотите удалить элемент?')) {
                                return;
                            }  
                            $(this).closest('tr').remove();
                            $.pjax.reload('#" . $pjaxId . "', {
                                url: $('#" . $formId . "').attr('action'),
                                replace: false,
                                timeout: 5000,
                                type: 'POST',
                                data: $('#" . $formId . "').serialize() + '&deleteTablePartRow=" . $relation . "'
                            });
                        });
                    ");
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                        'class' => 'delete-row',
                        'title' => 'Удалить строку',
                    ]);
                },
            ]
        ],
        'columns' => $controller::getTablePartColumns($model, $relation, $form),
    ],
]);
