<?php

/* @var $this yii\web\View */

/* @var ProductProvider $productProvider */

use common\models\reference\ProductProvider;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = 'WebFood';

$deleteChildButtonClass = 'delete-child-button';
$openAddMoneyModalClass = 'open-add-child-modal-button';
$addMoneyModalId = 'add-money-modal';
$addMoneyButtonId = 'add-money-button';
$moneyInputId = 'money-input';
$serviceObjectName = 'service-object-name-link';

$cards = [];
$children = [];
if ($productProvider->productProviderServiceObjects) {
    foreach ($productProvider->productProviderServiceObjects as $serviceObject) {
        $serviceObjects[$serviceObject->serviceObject->id]['name'] = $serviceObject->serviceObject->name;
        $serviceObjects[$serviceObject->serviceObject->id]['zip_code'] = $serviceObject->serviceObject->zip_code;
        $serviceObjects[$serviceObject->serviceObject->id]['city'] = $serviceObject->serviceObject->city;
        $serviceObjects[$serviceObject->serviceObject->id]['service_object_type'] = $serviceObject->serviceObject->serviceObjectType;
        $serviceObjects[$serviceObject->serviceObject->id]['address'] = $serviceObject->serviceObject->address;
        $serviceObjectIds[] = $serviceObject->serviceObject->id;
    }
}

echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-4 col-lg-4']);
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading']);
echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
echo Html::tag('span', 'Ваши заказчики', ['class' => 'panel-title h2']);
echo Html::endTag('span');
echo Html::endTag('div'); // panel-heading
if (!empty($serviceObjects)) {
    echo Html::beginTag('div', ['class' => 'list-group accordion', 'id' => 'child-list-accordion']);
    foreach ($serviceObjects as $serviceObjectId => $serviceObject) {
        echo Html::beginTag('div', ['class' => 'list-group-item list-group-item-action']);
        echo Html::beginTag('span', ['style' => 'display: flex; justify-content: space-between;']);
        echo Html::a($serviceObject['name'], '#childInfo-wrap-' . $serviceObjectId, [
            'class' => $serviceObjectName,
            'style' => 'text-decoration:none; border-bottom: 1px dashed #000080;',
        ]);
        echo Html::endTag('span');

        echo Html::beginTag('div', ['id' => 'childInfo-wrap-' . $serviceObjectId, 'class' => 'collapse']);

            echo Html::beginTag('p', ['style' => 'margin-top:10px;']);
            echo Html::tag('span', '<em class="text-muted">Школа:</em> ');
            echo Html::endTag('p');

        echo Html::endTag('div');
        echo Html::endTag('div'); // list-group-item list-group-item-action
    }
    echo Html::endTag('div'); // list-group accordion
}
echo Html::endTag('div'); // panel panel-default
echo Html::endTag('div'); // col-xs-4

echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-8 col-lg-8']);
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading child-buttons-panel']);
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'panel-body list-group card-history']);
echo 'Чтобы увидеть дополнительную информацию, выберите в списке ребенка.';
echo Html::endTag('div'); // panel-body
echo Html::endTag('div'); // panel panel-default
echo Html::endTag('div'); // col-xs-12 col-sm-6 col-md-8 col-lg-8


$this->registerJs("
    $('." . $serviceObjectName . "').click(function() { 
        var divs = $('#child-list-accordion').find('.collapse').hide();
        var div = $(this).attr('href'); 
        $(div).show();
        var childButtonsPanel = $('.child-buttons-panel');
        //$(childButtonsPanel).html('<button class=\"btn btn-success\">Заказать питание</button>');
        var cardId = $(this).data('card-id');
        if(cardId){
            $.ajax({
                url: '" . Url::to(['index']) . "',
                data: {'cardId' : cardId},
                dataType: 'json',
                type: 'POST',                    
                complete: function(response){   
                    $('.card-history').html(response.responseText);
                }
            });
        }
    });
");