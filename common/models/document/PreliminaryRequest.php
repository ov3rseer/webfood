<?php

namespace common\models\document;

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
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'preliminaryRequestProducts' => 'Продукты'
        ]);
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
