<?php

namespace common\models\register\registerConsolidate;

use common\components\DateTime;
use common\models\enum\DayType;
use yii\db\ActiveQuery;

/**
 * Модель "Производственный календарь"
 *
 * Свойства:
 * @property DateTime $date        дата
 * @property integer  $day_type_id тип дня
 *
 * Отношения:
 * @property DayType $dayType
 */
class Weekend extends RegisterConsolidate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['date', 'day_type_id'], 'required'],
            [['day_type_id'], 'integer'],
            'dateValidator' => [['date'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'date'          => 'Дата',
            'day_type_id'   => 'Тип дня',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getDayType()
    {
        return $this->hasOne(DayType::class, ['id' => 'day_type_id']);
    }

    /**
     * @inheritdoc
     */
    static public function getDimensions()
    {
        return [
            'date',
            'day_type_id',
        ];
    }
}