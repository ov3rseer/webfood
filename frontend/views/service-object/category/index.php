<?php

use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\reference\MealCategory;
use common\models\reference\ProductCategory;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var common\models\form\Report $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$reflection = new \ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridId = 'grid-' . $shortClassName;
$formId = 'form-' . $shortClassName;
$pjaxId = 'pjax-' . $formId;

$form = ActiveForm::begin([
    'id' => $formId,
    'method' => 'POST',
    'action' => Url::to(['']),
    'enableAjaxValidation' => false,
]);

echo Html::beginTag('div', ['class' => 'report-index']);
echo Html::beginTag('div', ['class' => 'report-attributes']);
echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-xs-4']);
foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
    if ($fieldOptions['displayType'] != ActiveField::HIDDEN) {
        echo $form->autoField($model, $field, $fieldOptions);
    }
}
echo Html::submitInput('Добавить', ['class' => 'btn btn-success']);
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'col-xs-8']);

$pjax = Pjax::begin([
    'id' => $pjaxId,
]);

/** @noinspection PhpUnhandledExceptionInspection */
echo GridViewWithToolbar::widget([
    'id' => $gridId,
    'gridToolbarOptions' => [
        'layout' => [['refresh', 'delete']],
    ],
    'gridOptions' => [
        'dataProvider' => $model->dataProvider,
        'actionColumn' => [
            'template' => '{update}{delete}',
            'buttons' => [
                'update' => function ($url, $rowModel) use ($pjaxId, $formId) {
                    /** @var MealCategory|ProductCategory $rowModel */
                    $showModalButtonId = 'show-modal-button'. $rowModel->id;
                    $this->registerJs("
                        $('#" . $showModalButtonId . "').click(function(){                          
                            var url = 'show';
                            var data = 'vapapapapaapapapapapapapapapapapapappaap';
                            $.get(url, {'data': data},function(data){
                                $('#events').modal('show');
                            });                                 
                        });
                    ");
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '#', [
                        'id' => $showModalButtonId,
                    ]);
                },
                'delete' => function ($url, $rowModel) use ($pjaxId) {
                    /** @var MealCategory|ProductCategory $rowModel */
                    $buttonId = 'delete-button-' . $rowModel->id;
                    $this->registerJs("
                        $('#" . $buttonId . "').click(function(e){
                            e.preventDefault();
                            if (!confirm('Вы действительно хотите удалить элемент?')) {
                                return;
                            }
                            var id = $(this).data('id');
                            $.ajax({
                                url: 'delete',
                                data: {id: id},
                                dataType: 'json',
                                type: 'POST',
                                success: function(data) {
                                    $.pjax.reload('#" . $pjaxId . "', {
                                        replace: true,
                                        timeout: 5000,
                                    });
                                }
                            });
                        });
                    ");
                    return Html::a('<span class="glyphicon glyphicon-remove"></span>', '#', [
                        'id' => $buttonId,
                        'data-id' => $rowModel->id
                    ]);
                }
            ],
        ],
        'columns' => $model->columns,
        'rowOptions' => function ($rowModel) {
            /** @var MealCategory|ProductCategory $rowModel */
            return $rowModel->is_active ? [] : ['style' => 'background-color: #f2dede;'];
        },
    ],
]);
$pjax->end();
$form->end();

echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
