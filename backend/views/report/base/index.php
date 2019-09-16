<?php

use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewToolbar;
use backend\widgets\GridView\GridViewWithToolbar;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var common\models\form\Report $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="report-index">

    <?php $form = ActiveForm::begin([
        'method' => 'GET',
        'action' => Url::to(['']),
        'enableAjaxValidation' => false,
    ]); ?>

    <div class="report-attributes">
        <div class="display:none;">
        <?php
            foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
                if ($fieldOptions['displayType'] == ActiveField::HIDDEN) {
                    echo $form->autoField($model, $field, $fieldOptions)->error(false)->label(false);
                }
            }
        ?>
        </div>
        <div class="row">
        <?php
            foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
                if ($fieldOptions['displayType'] != ActiveField::HIDDEN) {
                    echo '<div class="col-xs-12 col-sm-6 col-md-3">';
                    echo $form->autoField($model, $field, $fieldOptions);
                    echo '</div>';
                }
            }
        ?>
        </div>
    </div>

    <?= /** @noinspection PhpUnhandledExceptionInspection */
    GridViewToolbar::widget([
        'layout' => ['refresh'],
        'tokens' => [
            'refresh' => function() {
                return Html::submitInput('Сформировать', ['class' => 'btn btn-primary']);
            }
        ]
    ]); ?>

    <?php ActiveForm::end(); ?>

    <?= /** @noinspection PhpUnhandledExceptionInspection */
    GridViewWithToolbar::widget([
        'gridToolbarOptions' => false,
        'gridOptions' => [
            'dataProvider' => $model->dataProvider,
            'columns' => $model->columns,
            'checkboxColumn' => false,
            'actionColumn' => false,
        ],
    ]); ?>

</div>
