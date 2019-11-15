<?php

namespace common\models\document;

use common\models\enum\DocumentStatus;
use common\models\reference\CardChild;
use common\models\register\registerAccumulate\CardHistory;
use common\models\tablepart\PurchaseComplex;
use common\models\tablepart\PurchaseMeal;
use yii\db\ActiveQuery;

/**
 * Модель документа "Пополнение карты"
 *
 * Свойства:
 * @property integer $card_id
 * @property float $sum
 *
 * Отношения:
 * @property PurchaseComplex[] $purchaseComplex
 * @property PurchaseMeal[] $purchaseMeal
 * @property CardChild $card
 */
class RefillBalance extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Пополнение карты';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Пополнения карт';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['card_id'], 'integer'],
            [['sum'], 'number', 'min' => 0],
            [['card_id', 'sum'], 'required'],
            [['status_id'], 'validateStatus'],
        ]);
    }

    /**
     * Запрет на повторное проведение документа
     */
    public function validateStatus()
    {
        if (!$this->isNewRecord && $this->oldAttributes['status_id'] == DocumentStatus::POSTED) {
            $this->addError('summary', 'Пополнение счёта уже проведёно, невозможно изменить данные');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'card_id' => 'Карта',
            'sum' => 'Сумма',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(CardChild::class, ['id' => 'card_id']);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsForDependentRegisters()
    {
        return [
            // Заказы покупателей
            CardHistory::class => [
                'balance_error' => function () {
                    return '';
                },
                'function' => function (RefillBalance $documentModel) {
                    $result = [];
                    if ($documentModel) {
                        $result[] = [
                            'card_id' => $documentModel->card_id,
                            'sum' => $documentModel->sum,
                        ];

                    }
                    return $result;
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->status_id == DocumentStatus::POSTED) {
            $cardBalance = CardHistory::findBalance(null, ['sum'], ['card_id'], 't')->andWhere(['card_id' => $this->card_id])->one();
            if (!empty($cardBalance['sum'] && $this->card)) {
                $this->card->balance = $cardBalance['sum'];
                $this->card->save();
            }
        }
    }
}