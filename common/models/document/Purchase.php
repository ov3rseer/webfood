<?php

namespace common\models\document;

use common\models\enum\DocumentStatus;
use common\models\reference\CardChild;
use common\models\register\registerAccumulate\CardHistory;
use common\models\tablepart\PurchaseComplex;
use common\models\tablepart\PurchaseMeal;
use yii\db\ActiveQuery;

/**
 * Модель документа "Покупка"
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
class Purchase extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Покупка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Покупки';
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
        if ($this->oldAttributes['status_id'] == DocumentStatus::POSTED) {
            $this->addError('summary', 'Платёж уже проведён, невозможно изменить данные');
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
            'purchaseMeal' => 'Купленные блюда',
            'purchaseComplex' => 'Купленные комплексы',
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
     * @return ActiveQuery
     */
    public function getPurchaseComplex()
    {
        return $this->hasMany(PurchaseComplex::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getPurchaseMeal()
    {
        return $this->hasMany(PurchaseMeal::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'purchaseMeal' => PurchaseMeal::className(),
            'purchaseComplex' => PurchaseComplex::className(),
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     */
    public function getSettingsForDependentRegisters()
    {
        return [
            // История карты
            CardHistory::className() => [
                'balance_error' => function () {
                    $balanceRow = CardHistory::findBalance(null, ['sum'], [], 't')
                        ->andWhere(['t.card_id' => $this->card_id])
                        ->one();
                    if (!empty($balanceRow['sum'])) {
                        $result = $balanceRow['sum'] - $this->sum;
                        if ($result < 0) {
                            return 'Отрицательный баланс.';
                        }
                    }
                    return '';
                },
                'function' => function (Purchase $documentModel) {
                    $result = [];
                    if ($documentModel) {
                        $result[] = [
                            'card_id' => $documentModel->card_id,
                            'sum' => -1 * $documentModel->sum,
                        ];
                    }
                    return $result;
                },
            ],
        ];
    }
}