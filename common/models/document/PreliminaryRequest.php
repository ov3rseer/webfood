<?php


namespace common\models\document;


class PreliminaryRequest extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Предварительная заявка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Предварительные заявки';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_id', 'type_request_id'], 'integer'],
            [['product_id', 'type_request_id'], 'required'],
            [['quantity'], 'number'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_id' => 'Продукт',
            'type_request_id' => 'Тип заявки',
            'quantity' => 'Количество',
        ]);
    }
}