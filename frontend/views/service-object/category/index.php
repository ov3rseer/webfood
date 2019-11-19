<?php

use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\reference\MealCategory;
use common\models\reference\ProductCategory;
use frontend\models\serviceObject\CategoryForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var CategoryForm $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridId = $shortClassName . '-grid';
$formId = $shortClassName . '-form';
$pjaxId = $formId . '-pjax';

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
echo Html::submitInput('Сохранить', ['class' => 'btn btn-success']);
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
                    $modalId = 'update-modal-' . $rowModel->id;
                    $showModalButtonId = 'show-modal-button-' . $rowModel->id;
                    $updateButtonId = 'update-button-' . $rowModel->id;
                    unset($url);

                    Modal::begin([
                        'id' => $modalId,
                        'header' => '<h2>Изменить категорию</h2>',
                        'footer' => Html::button('Изменить', [
                            'id' => $updateButtonId,
                            'class' => 'btn btn-success',
                            'data-id' => $rowModel->id,
                        ]),
                    ]);
                    echo Html::beginTag('div', ['class' => 'row']);
                    echo Html::beginTag('div', ['class' => 'col-xs-12']);
                    echo Html::label('Наименование категории', 'category-name-' . $rowModel->id);
                    echo Html::textInput('category_name', Html::encode($rowModel), [
                        'id' => 'category-name-' . $rowModel->id,
                        'class' => 'form-control',
                    ]);
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['class' => 'row mt-3']);
                    echo Html::beginTag('div', ['class' => 'col-xs-12']);
                    echo Html::checkbox('is_active', $rowModel->is_active, ['id' => 'active-' . $rowModel->id, 'label' => 'Активен']);
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    Modal::end();

                    $this->registerJs("
                        $('#" . $showModalButtonId . "').click(function(){ 
                            $('#" . $modalId . "').modal('show');
                            $('#" . $updateButtonId . "').click(function(e){    
                                $('#" . $modalId . "').modal('hide');                   
                                e.preventDefault();
                                var id = $(this).data('id');
                                var category_name = $('#category-name-" . $rowModel->id . "').val();
                                var is_active = $('#active-" . $rowModel->id . "').is(':checked');
                                $('#".$modalId."').on('hidden.bs.modal', function () { 
                                    $.ajax({
                                        url: 'update',
                                        data: {id: id, is_active: is_active, category_name: category_name},
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
                    unset($url);
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
