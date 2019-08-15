<?php

namespace common\models\document;

use common\models\tablepart\OpenBankAccountChild;
use yii\db\ActiveQuery;

/**
 * Модель документа "Открытие счета"
 *
 * Отношения:
 * @property OpenBankAccountChild[] $children
 */
class OpenBankAccount extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Открытие счета';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Открытие счетов';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'children' => 'Данные детей для открытия счетов',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(OpenBankAccountChild::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'children' => OpenBankAccountChild::className(),
        ], parent::getTableParts());
    }
}