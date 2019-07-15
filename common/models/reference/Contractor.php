<?php

namespace common\models\reference;

use common\models\tablepart\ContractorContract;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Контрагенты"
 *
 * @property integer  $contractor_code
 * @property integer  $address
 * @property integer  $type_request_id
 *
 * Отношения:
 * @property ContractorContract $contractorContracts
 */
class Contractor extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Контрагент';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Контрагенты';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['contractor_code'], 'integer'],
            [['address'], 'string'],
            [['contractor_code'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contractor_code'       => 'Номер договора',
            'address'               => 'Адрес',
            'contractorContracts'   => 'Договора'
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractorContracts()
    {
        return $this->hasMany(ContractorContract::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'contractorContracts' => ContractorContract::className(),
        ], parent::getTableParts());
    }
}