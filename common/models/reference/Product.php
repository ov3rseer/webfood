<?php

namespace common\models\reference;

use yii\db\ActiveQuery;

/**
 * Модель справочника "Продукты"
 *
 * @property string   $product_code
 * @property integer  $unit_id
 *
 * Отношения:
 * @property Unit $unit
 */
class Product extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Продукт';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Продукты';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_code', 'unit_id'], 'integer'],
            [['product_code'], 'required'],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_code'  => 'Код продукта',
            'unit_id'       => 'Единица измерения',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::class, ['id' => 'unit_id']);
    }
}
