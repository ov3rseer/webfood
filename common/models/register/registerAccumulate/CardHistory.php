<?php

namespace common\models\register\registerAccumulate;

use common\models\reference\CardChild;
use common\models\reference\Child;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель регистра накопления "История карты"
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
        return 'Поступления товаров';
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

    /**
     * Получение истории движений по картам
     * @param integer $cardId
     * @return ActiveRecord[]
     * @throws InvalidConfigException
     */
    static public function getCardHistory($cardId)
    {
        return self::find()->andWhere(['card_id' => $cardId])->orderBy('date DESC')->all();
    }
}