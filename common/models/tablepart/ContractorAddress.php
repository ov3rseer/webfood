<?php

namespace common\models\tablepart;

use common\models\reference\Contract;
use common\models\reference\Contractor;

/**
 * Модель строки табличной части "Договоры с контрагентом" справочника "Контрагент"
 *
 * Свойства:
 * @property integer $contract_id
 *
 * Отношения:
 * @property Contractor    $parent
 * @property Contract      $contract
 */
class ContractorAddress extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address'], 'string'],
            [['address'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'address'    => 'Договор с контрагентом',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Contractor::className(), ['id' => 'parent_id']);
    }
}