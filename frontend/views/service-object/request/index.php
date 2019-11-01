<?php

use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewWithToolbar;
use frontend\models\serviceObject\RequestForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var RequestForm $model */
/* @var array $columns */
/* @var array $contracts */
/* @var array $dataProvider */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = $this->title;

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = $shortClassName . '-grid';
$formId = $shortClassName . '-form';
$pjaxId = $shortClassName . '-pjax';


$form = ActiveForm::begin([
    'id' => $formId,
    'method' => 'POST',
]);

echo Html::beginTag('div', ['class' => 'container']);
echo Html::beginTag('div', ['class' => 'row']);
echo Html::beginTag('div', ['class' => 'col-md-3']);
echo $form->field($model, 'service_object')->readonly();
echo $form->field($model, 'service_object_id')->hiddenInput();
echo Html::endTag('div');
echo Html::beginTag('div', ['class' => 'col-md-9']);
echo $form->field($model, 'contract_id')->dropDownList($contracts);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div'); //container

$pjax = Pjax::begin(['id' => $pjaxId]);

echo Html::beginTag('div', ['class' => 'container-fluid']);
/** @noinspection PhpUnhandledExceptionInspection */

echo GridViewWithToolbar::widget([
    'id' => $gridWidgetId,
    'gridToolbarOptions' => [
        'layout' => ['refresh', 'save'],
        'tokens' => [
            'refresh' => function () use ($formId, $pjaxId) {
                $buttonId = 'refresh-request-table';
                return Html::submitButton('Сформировать', [
                    'id' => $buttonId,
                    'class' => 'btn btn-primary',
                    'title' => 'Сформировать таблицу',
                ]);
            },
            'save' => function () use ($formId, $model) {
                $buttonId = 'save-request-table';
                $this->registerJs("
                    $('#" . $buttonId . "').click(function(e){             
                        var data = $('#" . $formId . "').serialize() + '&scenario=" . $model->scenario . "';           
                        $.ajax({
                            url: 'save-request-table',
                            replace: false,
                            timeout: 5000,
                            type: 'POST',
                            data: data,
                        });
                    });
                ");
                return Html::submitButton('Сохранить', [
                    'id' => $buttonId,
                    'class' => 'btn btn-success',
                    'title' => 'Сохранить заявку',
                ]);
            }
        ],
    ],
    'gridOptions' => [
        'dataProvider' => $dataProvider,
        'columns' => $columns,
        'checkboxColumn' => false,
        'actionColumn' => false,
    ]
]);
echo Html::endTag('div'); //container-fluid

$pjax->end();

ActiveForm::end();