<?php

namespace common\models\tablepart;

use common\models\reference\Contract;
use common\models\reference\Product;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Продукт" справочника "Договор"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $product_id
 *
 * Отношения:
 * @property Contract    $parent
 * @property Product     $product
 */
class ContractProduct extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id'], 'required'],
            [['product_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_id'    => 'Продукт',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Contract::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
}