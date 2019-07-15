<?php

namespace common\models\reference;

use common\components\DateTime;
use common\models\tablepart\ContractProduct;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Договоры"
 *
 * @property integer  $contract_code
 * @property DateTime $date_from
 * @property DateTime $date_to
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
            [['contract_code'], 'integer'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
            [['contract_number', 'date_from', 'date_to'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contract_code'   => 'Номер договора',
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
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'contractProducts' => ContractProduct::className(),
        ], parent::getTableParts());
    }
}