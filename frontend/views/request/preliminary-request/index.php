<?php

use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewToolbar;
use backend\widgets\GridView\GridViewWithToolbar;
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
    
        var selectsId = ['contractor_name', 'contract_code'];
        
        var smartSelect = new SmartSelect({
            'options' : {
                'contractor_name' : ".json_encode($model->contractor_name).",
                'contract_code' : ".json_encode($model->contract_code)."
            },
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
    $form = ActiveForm::begin([
        'method' => 'GET',
        'action' => Url::to(['']),
        'enableAjaxValidation' => false,
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
                return Html::submitInput('Сформировать', ['class' => 'btn btn-primary']);
            }
        ]
    ]);
    ?>

    <?php ActiveForm::end(); ?>

</div>
