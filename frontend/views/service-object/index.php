<?php

/* @var $this yii\web\View */

use common\models\enum\ContractType;
use common\models\enum\UserType;
use common\models\reference\ServiceObject;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$modalId = 'choice_contract_type_for_request';

$this->title = 'WebFood';

$serviceObject = null;
if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT) {
    $serviceObject = ServiceObject::findOne(['user_id' => Yii::$app->user->id]);
}

if ($serviceObject) {
    echo Html::beginTag('div', ['class' => 'site-index']);
    echo Html::beginTag('div', ['class' => 'jumbotron']);
    echo Html::beginTag('p');
    echo  Html::a('Предварительная заявка', null, [
        'id' => 'preliminary-request',
        'class' => 'btn btn-lg btn-success',
        'data-action' => 'serviceObject/request/index',
        'style' => 'width:300px;',
    ]);
    echo Html::endTag('p');
    echo Html::beginTag('p');
    echo Html::a('Корректировка заявки', null, [
        'id' => 'correction-request',
        'class' => 'btn btn-lg btn-success',
        'data-action' => 'serviceObject/request/index',
        'style' => 'width:300px;',
    ]);
    echo Html::endTag('p');
    echo Html::beginTag('p');
    echo Html::a('Заявки на открытие карт', 'serviceObject/open-card-request/index', [
        'class' => 'btn btn-lg btn-success',
        'style' => 'width:600px;',
    ]);
    echo Html::endTag('p');
    echo Html::endTag('div');
    echo Html::endTag('div');

    Modal::begin([
        'options' => [
            'id' => $modalId,
        ],
        'header' => 'Выберите тип договора',
    ]);
    echo Html::a('Дети', null, [
        'class' => 'btn btn-lg btn-success btn-block',
        'data-contract-type' => ContractType::CHILD,
    ]);
    echo Html::a('Сотрудники', null, [
        'class' => 'btn btn-lg btn-success btn-block',
        'data-contract-type' => ContractType::EMPLOYEES,
    ]);
    Modal::end();

    $this->registerJs(" 
        $('#preliminary-request, #correction-request').click(function(e) {
            var url = this.attributes['data-action'].value;
            var action = this.id;
            $('#".$modalId." a').each(function() {
                var contractTypeId = this.attributes['data-contract-type'].value;
                this.href = url + '?contractTypeId=' + contractTypeId + '&action=' + action;
            });
            $('#".$modalId."').modal('show');
        });"
    );
}