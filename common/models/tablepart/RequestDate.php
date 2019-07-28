<?php

namespace common\models\tablepart;

use common\components\DateTime;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Продукты" документа "Предварительная заявка"
 *
 * Свойства:
 * @property DateTime $week_day_date
 *
 * Отношения:
 * @property Request                $parent
 * @property RequestDateProduct[]   $requestDateProducts
 */
class RequestDate extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['week_day_date'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'week_day_date' => 'Дата',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Request::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestDateProducts()
    {
        return $this->hasMany(RequestDateProduct::class, ['request_date_id' => 'id']);
    }
}