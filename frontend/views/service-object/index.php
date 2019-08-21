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
    $this->registerJs(" 
        $('[data-action=\"serviceObject/request/preliminary-request/index\"], [data-action=\"serviceObject/request/correction-request/index\"]').click(function(e) {
            var action = this.attributes['data-action'].value;
            $('#" . $modalId . " a').each(function() {
                this.href = action + '?contractTypeId=' + this.attributes['data-contract-type'].value;
            });
            $('#" . $modalId . "').modal('show');
        });"
    );
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

    echo Html::beginTag('div', ['class' => 'site-index']);
    echo Html::beginTag('div', ['class' => 'jumbotron']);
    echo Html::beginTag('p');
    echo Html::a('Предварительная заявка', null, [
        'class' => 'btn btn-lg btn-success',
        'data-action' => 'serviceObject/request/preliminary-request/index',
        'style' => 'width:600px;',
    ]);
    echo Html::endTag('p');
    echo Html::beginTag('p');
    echo Html::a('Корректировка заявки', null, [
        'class' => 'btn btn-lg btn-success',
        'data-action' => 'serviceObject/request/correction-request/index',
        'style' => 'width:600px;',
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
}