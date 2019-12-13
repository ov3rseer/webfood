<?php

namespace common\models\register\registerAccumulate;

use common\models\reference\CardChild;
use common\models\reference\Child;
use yii\db\ActiveQuery;

/**
 * Модель регистра накопления "История карт"
 *
 * Свойства:
 * @property integer $card_id
 * @property float $sum
 *
 * Отношения:
 * @property CardChild $organization
 * @property Child $child
 */
class CardHistory extends RegisterAccumulate
{
    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'История карт';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['card_id', 'sum'], 'required'],
            [['sum'], 'number'],
            [['card_id'], 'integer'],
        ]);
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
    static public function getDimensions()
    {
        return [
            'card_id',
        ];
    }

    /**
     * @inheritdoc
     */
    static public function getResources()
    {
        return [
            'sum',
        ];
    }
}