<?php

use backend\controllers\reference\FileController;
use common\models\reference\File;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;
use backend\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var backend\models\form\UploadFileForm $model */

/** @var FileController $controller */
$controller = $this->context;

$fileModel = $model->file ?: new File();

if ($fileModel->isNewRecord) {
    $this->title = $fileModel->getSingularName() . ' (новый)';
    $this->params['breadcrumbs'][] = ['label' => $fileModel->getPluralName(), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = (string)$fileModel;
    $this->params['breadcrumbs'][] = ['label' => $fileModel->getPluralName(), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => (string)$fileModel, 'url' => ['update', 'id' => $fileModel->id]];
    $this->params['breadcrumbs'][] = 'Изменение';
}

$form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => ['enctype' => 'multipart/form-data'],
]);

$buttons = [
    Html::submitButton('Сохранить', ['class' => 'btn btn-primary']),
];

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="btn-toolbar">
                <?php
                /** @noinspection PhpUnhandledExceptionInspection */
                echo ButtonGroup::widget(['buttons' => $buttons]);
                ?>
            </div>
        </div>
    </div>
<?php

echo $form->field($model, 'name_full')->textInput();
echo $form->field($model, 'uploadedFile')->fileInput();
echo $form->field($model, 'comment')->textarea();

$form->end();
