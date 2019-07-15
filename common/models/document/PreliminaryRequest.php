<?php

namespace common\models\document;

use common\models\enum\ContractType;
use common\models\tablepart\PreliminaryRequestProduct;
use yii\db\ActiveQuery;

/**
 * Модель документа "Предварительная заявка"
 *
 * @property integer $type_request_id
 *
 * Отношения:
 * @property PreliminaryRequestProduct $preliminaryRequestProducts
 */
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
            [['type_request_id'], 'integer'],
            [['type_request_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type_request_id'            => 'Тип заявки',
            'preliminaryRequestProducts' => 'Продукты'
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getTypeRequest()
    {
        return $this->hasOne(ContractType::className(), ['id' => 'type_request_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPreliminaryRequestProducts()
    {
        return $this->hasMany(PreliminaryRequestProduct::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'preliminaryRequestProducts' => PreliminaryRequestProduct::className(),
        ], parent::getTableParts());
    }
}
