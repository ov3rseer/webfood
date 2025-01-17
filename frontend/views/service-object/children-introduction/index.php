<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridView;
use common\models\enum\UserType;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use frontend\models\serviceObject\ChildrenIntroductionForm;
use frontend\models\serviceObject\ChildrenIntroductionUploadFile;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var ChildrenIntroductionForm $model */
/* @var ChildrenIntroductionUploadFile $uploadFileForm */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$serviceObject = null;
if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT) {
    $serviceObject = ServiceObject::findOne(['user_id' => Yii::$app->user->id]);
}

if ($serviceObject) {
    $handInputButtonId = 'hand-input-button';
    $handInputModalId = 'hand-input-modal';
    $uploadFileButtonId = 'upload-file-button';
    $uploadFileModalId = 'upload-file-modal';

    $this->beginBlock('classes');
    echo GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => SchoolClass::find()->alias('t')->andWhere(['t.service_object_id' => $serviceObject->id]),
            'sort' => [
                'attributes' => [
                    'number',
                    'litter',
                ],
            ],
        ]),
        'actionColumn' => false,
        'checkboxColumn' => false,
        'columns' => [
            'name' =>[
                'format' => 'raw',
                'label' => 'Класс',
                'value' => function($rowModel){
                    /** @var SchoolClass $rowModel */
                    return $rowModel->name;
                }
            ],
            'children' => [
                'format' => 'raw',
                'label' => 'Ученики',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) {
                    /** @var SchoolClass $rowModel */
                    $result = '';
                    if (!empty($rowModel->schoolClassChildren)) {
                        $result = '<table class="table table-striped table-bordered">';
                        $result .= '<thead><tr><td><strong>Ученик</strong></td><td style="width: 250px"><strong>Подключен к питанию</strong></td></tr></thead><tbody>';
                        foreach ($rowModel->schoolClassChildren as $schoolClassChild) {
                            if (isset($schoolClassChild->child)) {
                                $result .= '<tr><td>' . Html::encode($schoolClassChild->child->name_full) . '</td><td>';
                                $result .= Html::checkbox('is_feeding', $schoolClassChild->child->is_feeding, ['data-child-id' => $schoolClassChild->child_id]) . '</td></tr>';
                            }
                        }
                        $result .= '</tbody></table>';
                    }
                    return $result;
                }
            ]
        ],
    ]);
    $this->endBlock();

    echo Html::beginTag('div', ['class' => 'container-fluid']);

    echo Html::beginTag('div', ['class' => 'row']);
    echo Html::tag('h3', 'Загрузка учащихся');
    echo Html::tag('p', 'Здесь можно добавить учеников вашей школы.');
    echo Html::tag('p', 'Перед использованием сервиса ознакомьтесь с инструкцией.');
    echo Html::beginTag('div', ['class' => 'input-group-btn']);
    echo Html::a('Ручной ввод', '#', ['id' => $handInputButtonId, 'class' => 'btn btn-success']);
    echo Html::a('Загрузка из файла', '#', ['id' => $uploadFileButtonId, 'class' => 'btn btn-success']);
    echo Html::endTag('div');
    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'row', 'style' => 'margin-top:25px;']);
    echo $this->blocks['classes'];
    echo Html::endTag('div');
    echo Html::endTag('div');

    $this->registerJs("
        $('[name = is_feeding]').click(function(e){ 
            var childId = $(this).data('child-id');
            var isFeeding = $(this).is(':checked');
            console.log(childId, isFeeding);
            $.ajax({
                url: 'set-on-food',
                method: 'POST',
                dataType: 'json',
                data: {childId: childId, isFeeding: isFeeding},            
            });
        });
        $('#" . $handInputButtonId . "').click(function(e){ 
            $('#" . $handInputModalId . "').modal('show');
        });
        $('#" . $uploadFileButtonId . "').click(function(e){
            $('#" . $uploadFileModalId . "').modal('show');
        });
    ");

    Modal::begin([
        'header' => '<h2>Ручной ввод</h2>',
        'options' => [
            'id' => $handInputModalId,
        ]
    ]);
    $form = ActiveForm::begin();
    echo Html::beginTag('div', ['class' => 'form-group']);
    echo $form->field($model, 'surname')->textInput();
    echo $form->field($model, 'forename')->textInput();
    echo $form->field($model, 'patronymic')->textInput();
    echo $form->field($model, 'class_number')->textInput(['type' => 'number']);
    echo $form->field($model, 'class_litter')->textInput();
    echo Html::submitButton('Сохранить данные', [
        'name' => 'action',
        'value' => 'hand-input',
        'class' => 'btn btn-success',
    ]);
    echo Html::endTag('div');
    ActiveForm::end();
    Modal::end();

    Modal::begin([
        'header' => '<h2>Загрузка из файла</h2>',
        'options' => [
            'id' => $uploadFileModalId,
        ]
    ]);
    $form = ActiveForm::begin();
    echo Html::beginTag('div', ['class' => 'form-group']);
    echo Html::beginTag('div', ['class' => 'row']);
    echo Html::beginTag('div', ['class' => 'col-xs-12']);
    echo Html::beginTag('div', ['class' => 'alert alert-info']);
    echo Html::beginTag('ul');
    echo Html::beginTag('li');
    echo Html::tag('strong', 'Формат файла: ');
    echo 'Excel (*.xls, *.xlsx), CSV (*.csv)';
    echo Html::endTag('li');
    echo Html::beginTag('li');
    echo Html::tag('strong', 'Колонки файла: ');
    echo Html::beginTag('ol');
    echo Html::tag('li', 'Фамилия');
    echo Html::tag('li', 'Имя');
    echo Html::tag('li', 'Отчество');
    echo Html::tag('li', 'Номер класса');
    echo Html::tag('li', 'Литера класса');
    echo Html::endTag('ol');
    echo Html::endTag('li');
    echo Html::beginTag('li');
    echo Html::tag('strong', 'Первая строка ');
    echo 'предназначена для заголовков колонок и пропускается при загрузке';
    echo Html::endTag('li');
    echo Html::beginTag('li');
    echo Html::tag('strong', 'Файл-образец: ');
    echo Html::a('Скачать', ['download-example-file'], ['target' => '_blank']);
    echo Html::endTag('li');
    echo Html::endTag('ul');
    echo Html::endTag('div');
    echo Html::endTag('div');
    echo Html::endTag('div');
    echo Html::endTag('div');

    echo $form->field($uploadFileForm, 'uploadedFile')->fileInput();
    echo Html::submitButton('Загрузить данные из файла', [
        'name' => 'action',
        'value' => 'upload-file',
        'class' => 'btn btn-success',
    ]);
    ActiveForm::end();
    Modal::end();
}