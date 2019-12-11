<?php

namespace common\models\document;

use common\models\reference\ProductProvider;
use common\models\tablepart\ArrivalProduct;
use yii\db\ActiveQuery;

/**
 * Модель документа "Поступление продуктов"
 *
 * Свойства:
 * @property integer $product_provider_id
 *
 * Отношения:
 * @property ProductProvider $productProvider
 * @property ArrivalProduct[] $arrivalProducts
 */
class Arrival extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Поступление продуктов';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Поступления продуктов';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_provider_id'], 'integer'],
            [['product_provider_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_provider_id' => 'Поставщик продуктов',
            'arrivalProducts' => 'Продукты',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductProvider()
    {
        return $this->hasOne(ProductProvider::class, ['id' => 'product_provider_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArrivalProducts()
    {
        return $this->hasMany(ArrivalProduct::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        $result = parent::getTableParts();
        if ($this->product_provider_id) {
            $result = array_merge([
                'arrivalProducts' => ArrivalProduct::class,
            ], $result);
        }
        return $result;
    }
}