<?php
use backend\controllers\system\SystemController;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;
use backend\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var backend\models\system\ImportContractorAndContractForm $model */

/** @var SystemController $controller */
$controller = $this->context;

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => (string)$this->title];

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();

$formId = $shortClassName.'-form';
if ($info = Yii::$app->session->getFlash('proceed')) {
    ?>
    <div class="alert alert-info" role="alert">
        <?= $info ?>
    </div>
    <?php
}

$form = ActiveForm::begin(
    [
        'id'                   => $formId,
        'enableAjaxValidation' => true,
        'options'              => ['enctype' => 'multipart/form-data'],
    ]
);
?>
    <div class="form-attributes">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-info" role="alert">
                        <ul>
                            <li>
                                <strong>Формат файла:</strong> XML (*.xml)
                            </li>
                            <li>
                                <strong>Файл-образец:</strong> <?= Html::a('Скачать', ['download-example-file'], ['target' => '_blank']) ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php
                //foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
                    echo '<div class="col-xs-12 col-sm-6 col-md-3">';
                    echo $form->field($model, 'uploadedFiles[]')->fileInput(['multiple' => true]);
                    echo '</div>';
                //}
                ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <?php
                    $buttons = [
                        Html::submitButton('Загрузить файл', ['class' => 'btn btn-success']),
                    ];
                    echo ButtonGroup::widget(['buttons' => $buttons]);
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php

$form->end();