<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridView;
use backend\widgets\Select2\Select2;
use common\models\enum\FoodType;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use frontend\models\serviceObject\MealForm;
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
                <div class="col-xs-6">
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
                </div>
                <div class="col-xs-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <strong>Состав:</strong>
                            <div id="add-field-area">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6">
                                        <?=
                                        $form->field($model, 'products', [
                                            'template' => "{input}\n{hint}\n{error}",
                                            'inputOptions' => [
                                                'id' => Html::getInputId($model, '[products]product_id'),
                                                'name' => Html::getInputName($model, '[products]product_id'),
                                                'class' => 'form-control',
                                            ],
                                        ])->widget(Select2::class, ['items' => $model->getProducts()]);
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <?=
                                        $form->field($model, 'products', [
                                            'template' => "{input}\n{hint}\n{error}",
                                            'inputOptions' => [
                                                'id' => Html::getInputId($model, '[products]quantity'),
                                                'name' => Html::getInputName($model, '[products]quantity'),
                                                'class' => 'form-control',
                                            ],
                                        ])->textInput();
                                        ?>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <?=
                                        $form->field($model, 'products', [
                                            'template' => "{input}\n{hint}\n{error}",
                                            'inputOptions' => [
                                                'id' => Html::getInputId($model, '[products]unit_id'),
                                                'name' => Html::getInputName($model, '[products]unit_id'),
                                                'class' => 'form-control',
                                            ],
                                        ])->widget(Select2::class, ['pluginOptions' => [
                                        ], 'items' => $model->getUnits()]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?= Html::button('Добавить', ['id' => 'add-meal-products', 'class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
                </div>
            </div>

            <?php
            $this->registerJs("
            $('#add-meal-products').click(function(e){
                e.preventDefault();
                
                $('div #add-field-area').append(row);
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
    </div>
<?php
$form->end();
