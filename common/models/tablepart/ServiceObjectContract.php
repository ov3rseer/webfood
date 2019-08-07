<?php

namespace common\models\tablepart;

use common\models\reference\Contract;
use common\models\reference\ServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Договора с объектом обслуживания" справочника "Объект обслуживания"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $contract_id
 * @property string  $address
 *
 * Отношения:
 * @property ServiceObject  $parent
 * @property Contract       $contract
 */
class ServiceObjectContract extends TablePart
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
            'contract_id'    => 'Договор с объектом обслуживания',
            'address'        => 'Адрес',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ServiceObject::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::className(), ['id' => 'contract_id']);
    }
}