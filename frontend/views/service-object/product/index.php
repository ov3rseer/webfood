<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\reference\Product;
use common\models\reference\ProductCategory;
use common\models\reference\Unit;
use frontend\models\serviceObject\ProductForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var ProductForm $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridId = $shortClassName . '-grid';
$formId = $shortClassName . '-form';
$pjaxId = $formId . '-pjax';

$form = ActiveForm::begin([
    'method' => 'POST',
    'action' => Url::to(['']),
    'enableAjaxValidation' => false,
]);

$units = Unit::find()->select('name_full')->indexBy('id')->column();
$productCategories = ProductCategory::find()->select('name')->indexBy('id')->column();
echo Html::beginTag('div', ['class' => 'report-index']);
echo Html::beginTag('div', ['class' => 'report-attributes']);
echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-xs-4']);
echo $form->field($model, 'name')->textInput(['class' => 'form-control']);
echo $form->field($model, 'product_code')->textInput(['class' => 'form-control']);
echo $form->field($model, 'price')->textInput(['class' => 'form-control']);
echo $form->field($model, 'unit_id')->dropDownList($units, ['class' => 'form-control']);
echo $form->field($model, 'product_category_id')->dropDownList($productCategories, ['class' => 'form-control']);
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
                'update' => function ($url, $rowModel) use ($pjaxId, $formId, $units, $productCategories) {
                    /** @var Product $rowModel */
                    $modalId = 'update-modal-' . $rowModel->id;
                    $showModalButtonId = 'show-modal-button-' . $rowModel->id;
                    $updateButtonId = 'update-button-' . $rowModel->id;
                    unset($url);

                    Modal::begin([
                        'id' => $modalId,
                        'header' => '<h2>Изменить продукт</h2>',
                        'footer' => Html::button('Изменить', [
                            'id' => $updateButtonId,
                            'class' => 'btn btn-success',
                            'data-id' => $rowModel->id,
                        ]),
                    ]);
                    echo Html::beginTag('div', ['class' => 'row']);
                    echo Html::beginTag('div', ['class' => 'col-xs-12']);
                    echo Html::label('Наименование продукта', 'product-name-' . $rowModel->id);
                    echo Html::textInput('product_name', Html::encode($rowModel), [
                        'id' => 'product-name-' . $rowModel->id,
                        'class' => 'form-control',
                    ]);
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['class' => 'row mt-3']);
                    echo Html::beginTag('div', ['class' => 'col-xs-12']);
                    echo Html::checkbox('is_active', $rowModel->is_active, ['id' => 'active-' . $rowModel->id, 'label' => 'Активен']);
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['class' => 'row mt-3']);
                    echo Html::beginTag('div', ['class' => 'col-xs-6']);
                    echo Html::label('Код продукта', 'product-code-' . $rowModel->id);
                    echo Html::textInput('product_code', $rowModel->product_code, ['id' => 'product-code-' . $rowModel->id, 'class' => 'form-control']);
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['class' => 'col-xs-6']);
                    echo Html::label('Цена', 'product-price-' . $rowModel->id);
                    echo Html::textInput('price', $rowModel->price, ['id' => 'product-price-' . $rowModel->id, 'class' => 'form-control']);
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['class' => 'row mt-3']);
                    echo Html::beginTag('div', ['class' => 'col-xs-6']);
                    echo Html::label('Ед. измерения', 'product-unit-' . $rowModel->id);
                    echo Html::dropDownList('unit_id', $rowModel->unit_id, $units, ['id' => 'product-unit-' . $rowModel->id, 'class' => 'form-control']);
                    echo Html::endTag('div');
                    echo Html::beginTag('div', ['class' => 'col-xs-6']);
                    echo Html::label('Категория продукта', 'product-category-' . $rowModel->id);
                    echo Html::dropDownList('product_category_id', $rowModel->product_category_id, $productCategories, ['id' => 'product-category-' . $rowModel->id, 'class' => 'form-control']);
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
                                var name = $('#product-name-" . $rowModel->id . "').val();
                                var is_active = $('#active-" . $rowModel->id . "').is(':checked');
                                var product_code = $('#product-code-" . $rowModel->id . "').val();
                                var price = $('#product-price-" . $rowModel->id . "').val();
                                var unit_id = $('#product-unit-" . $rowModel->id . "').val();
                                var category_id = $('#product-category-" . $rowModel->id . "').val();
                                $('#".$modalId."').on('hidden.bs.modal', function () {                 
                                    $.ajax({
                                        url: 'update',
                                        data: {id: id, is_active: is_active, name: name, product_code: product_code, price: price, unit_id: unit_id, category_id: category_id},
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
                    /** @var Product $rowModel */
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
            /** @var Product $rowModel */
            return $rowModel->is_active ? [] : ['style' => 'background-color: #f2dede;'];
        },
    ]
]);
$pjax->end();
$form->end();

echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');