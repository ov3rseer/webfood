<?php

use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewToolbar;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var frontend\models\request\PreliminaryRequestForm $model */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

$fields = $model->getFieldsOptions();
$logic = $model->logic;
unset($fields['logic']);

$this->registerJs("
    $().ready(function() {
    
        var selectsId = ['service_object_name', 'contract_code'];
        
        var smartSelect = new SmartSelect({
            'mainLogic' : ".json_encode($logic).",
            'selectsId' : selectsId
        });
    
        smartSelect.createChangeHandler();        
        smartSelect.init();    
    
    });
");

?>
<div class="reference-index">

    <?php
    if ($fields && !empty($model->service_object_name) && !empty($model->contract_code)) {
        $form = ActiveForm::begin([
            'method' => 'GET',
            'action' => Url::to(['']),
            'enableAjaxValidation' => false,
            'options' => ['class' => 'container'],
        ]);
        ?>

        <div class="report-attributes">
            <div class="row">
                <?php
                foreach ($fields as $field => $fieldOptions) {
                    if ($fieldOptions['displayType'] != ActiveField::HIDDEN) {
                        echo '<div class="col-md-6">';
                        echo $form->field($model, $field)->dropDownList($model->{$field}, ['id' => $field]);
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <?=
        /** @noinspection PhpUnhandledExceptionInspection */
        GridViewToolbar::widget([
            'layout' => ['refresh'],
            'tokens' => [
                'refresh' => function() {
                    return Html::button('Сформировать', ['class' => 'btn btn-primary', 'onclick' => 'createRequestTable("preliminary_request")']);
                }
            ]
        ]);
        ?>

        <?php
        ActiveForm::end();
    }
    ?>

    <div class="container-fluid">
        <div class="embed-responsive embed-responsive-16by9" id="main_request_table" style="margin-top: 1em;"></div>
    </div>

</div>
