<?php

namespace common\models\reference;

use common\components\DateTime;
use common\models\enum\ContractType;
use common\models\tablepart\ContractProduct;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Договоры"
 *
 * @property integer  $contract_code
 * @property integer  $contract_type_id
 * @property DateTime $date_from
 * @property DateTime $date_to
 *
 * Отношения:
 * @property ContractProduct $contractProducts
*/
class Contract extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Договор с контрагентом';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Договоры с контрагентами';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['contract_code', 'contract_type_id'], 'integer'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
            [['contract_code', 'contract_type_id', 'date_from', 'date_to'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contract_code'     => 'Номер договора',
            'contract_type_id'  => 'Тип договора',
            'date_from'         => 'Дата начала',
            'date_to'           => 'Срок действия',
            'contractProducts'  => 'Продукты',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractProducts()
    {
        return $this->hasMany(ContractProduct::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */

    public function getContractType()

    {
        return $this->hasOne(ContractType::className(), ['id' => 'contract_type_id']);
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'contractProducts' => ContractProduct::className(),
        ], parent::getTableParts());
    }
}