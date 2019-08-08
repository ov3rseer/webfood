<?php

/* @var $this yii\web\View */

use common\models\enum\ContractType;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

$modalId = 'choice_contract_type_for_request';

$this->title = 'WebFood';

$this->registerJs(" 
    $('[data-action=\"request/preliminary-request/index\"], [data-action=\"request/correction-request/index\"]').click(function(e) {
        var action = this.attributes['data-action'].value;
        $('#".$modalId." a').each(function() {
            this.href = action + '?contractTypeId=' + this.attributes['data-contract-type'].value;
        });
        $('#".$modalId."').modal('show');
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
?>
<div class="site-index">

    <div class="jumbotron">

    </div>

</div>
