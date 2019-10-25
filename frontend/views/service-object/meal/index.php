<?php

use backend\widgets\GridView\GridView;
use backend\widgets\Select2\Select2;
use common\models\enum\FoodType;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use frontend\models\serviceObject\MealForm;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var MealForm $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$gridPjaxId = 'id-grid';

$foodTypes = FoodType::find()->select('name')->indexBy('id')->column();
$productCategories = MealCategory::find()->select('name')->indexBy('id')->column();
$form = ActiveForm::begin([
    'method' => 'POST',
    'action' => Url::to(['']),
    'enableAjaxValidation' => false,
]);

?>
    <div class="report-index">
    <div class="report-attributes">
        <div class="row">
            <div class="col-xs-5">
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Блюдо:</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'name')->textInput(['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <?= $form->field($model, 'meal_category_id')->dropDownList($productCategories, ['class' => 'form-control']); ?>
                    </div>
                    <div class="col-xs-6">
                        <?= $form->field($model, 'food_type_id')->dropDownList($foodTypes, ['class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'description')->textarea(['class' => 'form-control', 'style' => 'max-width:100%; min-width:100%;']) ?>
                    </div>
                </div>
                <div class="row">
                    <div id='meal-products' class="col-xs-12">
                        <h5>Состав блюда:</h5>
                        <p id="empty-products">
                            Пусто
                        </p>
                        <?= Html::hiddenInput(Html::getInputName($model, 'products'), '{}', ['id' => Html::getInputId($model, 'products')]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4>Изменить состав блюда:</h4>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-xs-12 col-sm-5">
                                <?=
                                $form->field($model, 'products', [
                                    'inputOptions' => [
                                        'id' => Html::getInputId($model, '[products]product_id'),
                                        'name' => Html::getInputName($model, '[products]product_id'),
                                    ],
                                ])
                                    ->widget(Select2::class, [
                                        'items' => $model->getProducts(),
                                        'pluginOptions' => [
                                            'placeholder' => 'Выберите значение...',
                                            'allowClear' => true,
                                            'width' => null,
                                        ],
                                    ])
                                    ->label('Продукт');
                                ?>
                            </div>
                            <div class="col-xs-12 col-sm-3">
                                <?=
                                $form->field($model, 'products', [
                                    'inputOptions' => [
                                        'id' => Html::getInputId($model, '[products]quantity'),
                                        'name' => Html::getInputName($model, '[products]quantity'),
                                    ],
                                ])
                                    ->textInput()
                                    ->label('Количество(вес)');
                                ?>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <?=
                                $form->field($model, 'products', [
                                    'inputOptions' => [
                                        'id' => Html::getInputId($model, '[products]unit_id'),
                                        'name' => Html::getInputName($model, '[products]unit_id'),
                                    ],
                                ])
                                    ->widget(Select2::class, [
                                        'items' => $model->getUnits(),
                                        'pluginOptions' => [
                                            'placeholder' => 'Выберите значение...',
                                            'allowClear' => true,
                                            'width' => null,
                                        ],
                                    ])
                                    ->label('Ед. измерения');
                                ?>
                            </div>
                        </div>
                        <?= Html::button('Добавить', ['id' => 'add-meal-products', 'class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
        </div>


        <?php
        $this->registerJs("
            $('#add-meal-products').click(function(e){
                e.preventDefault();                
                var productInput = $('#" . Html::getInputId($model, '[products]product_id') . "');
                var unitInput = $('#" . Html::getInputId($model, '[products]unit_id') . "');
                var quantityInput = $('#" . Html::getInputId($model, '[products]quantity') . "');                   
                if(!productInput.val()){
                    alert('Не выбран продукт.');
                    return;
                };        
                if(!quantityInput.val()){
                    alert('Укажите количество(вес).');
                    return;
                };          
                if(!unitInput.val()){
                    alert('Не выбрана ед. измерения.');
                    return;
                };                        
                
                var selectedProduct = productInput.find('option:selected');
                var selectedUnit = unitInput.find('option:selected');
               
                $('#empty-products').remove();
                $('#meal-products').append(
                    '<span>' + selectedProduct.text() + ' - ' + quantityInput.val() + ' ' + selectedUnit.text() + '</span>' 
                    + '<a id=\"\"><span class=\"glyphicon glyphicon-remove\"></span></a>'
                );
              
                var hiddenInput = $('#" . Html::getInputId($model, 'products') . "');
                
                var product = JSON.parse(hiddenInput.val());
                product[selectedProduct.val()] = {'quantity': quantityInput.val(), 'unit_id': selectedUnit.val()};
                hiddenInput.val(JSON.stringify(product));
        
                productInput.select2('val', '');
                unitInput.select2('val', '');
                quantityInput.val('');                
                selectedProduct.remove();
            });
        ");

        ?>
        <div class="row" style="margin-top: 30px;">
            <?php
            $pjax = Pjax::begin();

            /** @noinspection PhpUnhandledExceptionInspection */
            echo GridView::widget([
                'dataProvider' => new ActiveDataProvider([
                    'query' => Meal::find()->andWhere(['is_active' => true]),
                ]),
                'actionColumn' => false,
                'checkboxColumn' => false,
                'columns' => $model->columns,
            ]);

            $pjax->end();
            ?>
        </div>
    </div>
<?php
$form->end();
