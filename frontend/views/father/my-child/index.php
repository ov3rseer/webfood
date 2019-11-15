<?php

/* @var $this yii\web\View */
/* @var Father $father */

use common\models\reference\Father;
use yii\bootstrap\Html;

$this->title = 'WebFood';

$cards = [];
$children = [];
if ($father->fatherChildren) {
    foreach ($father->fatherChildren as $fatherChild) {
        $children[$fatherChild->child_id]['name'] = $fatherChild->child->name_full ?? $fatherChild->child->name;
        if ($fatherChild->child->serviceObject) {
            $children[$fatherChild->child_id]['serviceObject'] = Html::encode($fatherChild->child->serviceObject);
        }
        if ($fatherChild->child->schoolClass) {
            $children[$fatherChild->child_id]['schoolClass'] = Html::encode($fatherChild->child->schoolClass);
        }
        if ($card = $fatherChild->child->card) {
            $cards[$fatherChild->child_id]['id'] = $card->id;
            $cards[$fatherChild->child_id]['card_number'] = $card->card_number;
            $cards[$fatherChild->child_id]['balance'] = $card->balance;
        }
    }
}

echo Html::beginTag('div', ['class' => 'col-xs-12 col-sm-6 col-md-4 col-lg-4']);
echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::beginTag('div', ['class' => 'panel-heading']);
echo $this->render('_addChildPanel');
echo Html::endTag('div'); // panel-heading
if (!empty($children)) {
    echo Html::beginTag('div', ['class' => 'list-group accordion', 'id' => 'child-list-accordion']);
    foreach ($children as $childId => $child) {
        echo Html::beginTag('div', ['class' => 'list-group-item list-group-item-action']);
        echo $this->render('_childrenList', ['child' => $child, 'childId' => $childId, 'card' => $cards[$childId]]);
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