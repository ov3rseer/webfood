<?php

use common\models\document\Purchase;
use common\models\document\RefillBalance;
use common\models\register\registerAccumulate\CardHistory;
use common\models\system\Entity;
use yii\helpers\Html;

/** @var integer $cardId */
/** @var CardHistory[] $cardHistories */
$cardHistories = CardHistory::find()->andWhere(['card_id' => $cardId])->orderBy('date DESC')->all();
if ($cardHistories) {
    foreach ($cardHistories as $cardHistory) {
        $modelClass = Entity::getClassNameById($cardHistory->document_basis_type_id);
        /** @var RefillBalance|Purchase $document */
        $document = $modelClass::findOne(['id' => $cardHistory->document_basis_id]);

        echo Html::beginTag('div', ['class' => 'row list-group-item']);
        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo $cardHistory->date;
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo $document->getSingularName() . ' ' . $document->card->card_number;
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo $cardHistory->sum . ' &#8381;';
        echo Html::endTag('div');
        echo Html::endTag('div');
    }
}