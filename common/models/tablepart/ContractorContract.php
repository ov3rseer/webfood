<?php

namespace common\models\tablepart;

use common\models\reference\Contract;
use common\models\reference\Contractor;

/**
 * Модель строки табличной части "Договоры с контрагентом" справочника "Контрагент"
 *
 * Свойства:
 * @property integer $contract_id
 * @property string  $address
 *
 * Отношения:
 * @property Contractor    $parent
 * @property Contract      $contract
 */
class ContractorContract extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['contract_id'], 'integer'],
            [['contract_id', 'address'], 'required'],
            [['address'], 'string'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contract_id'    => 'Договор с контрагентом',
            'address'        => 'Адрес',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Contractor::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
    }
}