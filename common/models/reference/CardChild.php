<?php

namespace common\models\reference;

/**
 * Модель справочника "Карты детей"
 *
 * @property string $card_number
 * @property float $balance
 * @property float $limit_per_day
 */
class CardChild extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Карта ребёнка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Карты детей';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['card_number'], 'string'],
            [['balance', 'limit_per_day'], 'number'],
            [['card_number', 'balance', 'limit_per_day'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'card_number' => 'Номер карты',
            'balance' => 'Баланс',
            'limit_per_day' => 'Лимит в день',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            $this->name = $this->card_number;
        }
        return $parentResult;
    }
}