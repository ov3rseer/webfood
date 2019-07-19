<?php

namespace common\models\reference;

use common\models\tablepart\ContractorAddress;
use common\models\tablepart\ContractorContract;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Контрагенты"
 *
 * @property integer  $contractor_code
 * @property integer  $user_id
 * @property integer  $type_request_id
 *
 * Отношения:
 * @property User               $user
 * @property ContractorContract $contractorContract
 * @property ContractorAddress  $contractorAddresses
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
            [['contractor_code', 'user_id'], 'integer'],
            [['contractor_code'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contractor_code'       => 'Номер контрагента',
            'user_id'               => 'Прикреплённый пользователь',
            'contractorContracts'   => 'Договора',
            'contractorAddresses'   => 'Адреса',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
     * @return ActiveQuery
     */
    public function getContractorAddresses()
    {
        return $this->hasMany(ContractorAddress::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'contractorContracts' => ContractorContract::className(),
            'contractorAddresses' => ContractorAddress::className(),
        ], parent::getTableParts());
    }
}