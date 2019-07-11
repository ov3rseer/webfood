<?php


namespace common\models\reference;


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
            [['unit_id', 'product_code'], 'integer'],
            [['product_code', 'unit_id'], 'required'],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'unit_id' => 'Единица измерения',
            'product_code' => 'Код продукта',
        ]);
    }
}