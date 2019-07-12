<?php

namespace common\models\reference;

/**
 * Модель "Продукт"
 *
 * @property string  $product_code
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
            [['product_code'], 'integer'],
            [['product_code'], 'required'],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_code' => 'Код продукта',
        ]);
    }
}
