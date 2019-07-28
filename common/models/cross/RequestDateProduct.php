<?php

namespace common\models\cross;

use common\models\reference\Product;
use common\models\reference\Unit;
use common\models\tablepart\RequestDate;
use yii\db\ActiveQuery;

/**
 * Модель кросс-таблицы для хранения связей между датами и продуктами заявки
 *
 * @property integer $request_date_id
 * @property integer $product_id
 * @property integer $unit_id
 * @property string  $planned_quantity
 * @property string  $current_quantity
 *
 * Отношения:
 * @property Product        $product
 * @property Unit           $unit
 * @property RequestDate    $parent
 */
class RequestDateProduct extends CrossTable
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['request_date_id', 'product_id', 'unit_id'], 'required'],
            [['request_date_id', 'product_id', 'unit_id'], 'integer'],
            [['planned_quantity', 'current_quantity'], 'number', 'min' => 0]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'request_date_id'   => 'Дата',
            'product_id'        => 'Продукт',
            'unit_id'           => 'Единица измерения',
            'planned_quantity'  => 'Планируемое количество',
            'current_quantity'  => 'Фактическое количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(RequestDate::class, ['id' => 'request_date_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }
}