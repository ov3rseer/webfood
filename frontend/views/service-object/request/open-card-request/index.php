<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridView;
use common\models\document\OpenBankAccount;
use common\models\enum\DocumentStatus;
use common\models\enum\UserType;
use common\models\reference\ServiceObject;
use frontend\models\serviceObject\OpenCardRequest;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var OpenCardRequest $model */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$handInputButtonId = 'hand-input-button';
$handInputModalId = 'hand-input-modal';
$uploadFileButtonId = 'upload-file-button';
$uploadFileModalId = 'upload-file-modal';

if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT) {
    $serviceObject = ServiceObject::findOne(['user_id' => Yii::$app->user->id]);
}

if ($serviceObject) {
    echo Html::beginTag('div', ['class' => 'container-fluid']);
    echo Html::beginTag('div', ['class' => 'col-xs-4']);
    echo Html::tag('h1', Html::encode($this->title));
    echo Html::tag('p', 'Здесь можно сформировать заявку на открытие карт новичкам.');
    echo Html::tag('p', 'Перед использованием сервиса ознакомьтесь с инструкцией.');

    echo Html::beginTag('div', ['class' => 'input-group-btn']);
    echo Html::a('Ручной ввод', '#', ['id' => $handInputButtonId, 'class' => 'btn btn-success']);
    echo Html::a('Загрузка из файла', '#', ['id' => $uploadFileButtonId, 'class' => 'btn btn-success']);
    echo Html::endTag('div');

    echo Html::endTag('div');
    echo Html::beginTag('div', ['class' => 'col-xs-8']);
    echo Html::tag('h1', 'Заявки');

    Pjax::begin();
    echo GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => OpenBankAccount::find()->andWhere(['service_object_id' => $serviceObject->id])->orderBy('id DESC'),
        ]),
        'actionColumn' => false,
        'checkboxColumn' => false,
        'columns' => [
            [
                'attribute' => 'id',
                'label' => 'Заявка',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var OpenBankAccount $rowModel */
                    return Html::encode((string)$rowModel);
                },
            ],
            [
                'attribute' => 'status_id',
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var OpenBankAccount $rowModel */
                    $status = '';
                    switch ($rowModel->status_id) {
                        case DocumentStatus::DRAFT:
                            $status = 'Принят';
                            break;
                        case DocumentStatus::POSTED:
                            $status = 'Обработан';
                            break;
                        case DocumentStatus::DELETED:
                            $status = 'Удален';
                            break;
                    };
                    return $status;
                },
            ],
        ],
    ]);
    Pjax::end();

    echo Html::endTag('div');
    echo Html::endTag('div');
}

$this->registerJs("
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
echo $form->field($model, 'codeword')->textInput();
echo $form->field($model, 'snils')->textInput();
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
                            echo Html::tag('li', 'Кодовое слово');
                            echo Html::tag('li', 'СНИЛС');
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

echo $form->field($model, 'uploadedFile')->fileInput();
echo Html::submitButton('Загрузить данные из файла', [
    'name' => 'action',
    'value' => 'upload-file',
    'class' => 'btn btn-success',
]);
ActiveForm::end();
Modal::end();