<?php


namespace common\models\reference;


use common\components\DateTime;

/**
 * Модель справочник "Договор"
 *
 * @property integer  $contract_number
 * @property DateTime $date_from
 * @property DateTime $date_to
 * @property integer  $contractor_id
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
            [['contract_number', 'contractor_id'], 'integer'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
            [['contract_number', 'contractor_id', 'date_from', 'date_to'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contract_number' => 'Номер договора',
            'date_from' => 'Дата начала',
            'date_to' => 'Срок действия',
            'contractor_id' => 'Ссылка на контрагента',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Contractor::className(), ['id' => 'contractor_id']);
    }
}