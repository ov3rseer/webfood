<?php

namespace common\models\document;

use backend\controllers\document\DocumentController;
use backend\widgets\ActiveForm;
use common\models\enum\ContractType;
use common\models\reference\Contract;
use common\models\reference\Contractor;
use common\models\tablepart\RequestDate;
use ReflectionException;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Модель документа "Предварительная заявка"
 *
 * @property integer $contract_type_id
 * @property integer $contract_id
 * @property integer $contractor_id
 * @property string  $contractor_code
 * @property string  $contract_code
 * @property string  $address
 *
 * Отношения:
 * @property RequestDate[]  $requestDates
 * @property ContractType   $contractType
 * @property Contractor     $contractor
 * @property Contract       $contract
 */
class Request extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Заявка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Заявки';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['contract_id', 'contractor_id', 'contract_type_id'], 'integer'],
            [['address', 'contract_code', 'contractor_code'], 'string'],
            [['contractor_code', 'contract_code', 'contract_type_id', 'address', 'contractor_id', 'contract_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contract_type_id'  => 'Тип заявки',
            'contractor_id'     => 'Контрагент',
            'contractor_code'   => 'Код контрагента',
            'contract_id'       => 'Контракт',
            'contract_code'     => 'Код контракта',
            'address'           => 'Место поставки',
            'requestDates'      => 'Дни недели',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestDates()
    {
        return $this->hasMany(RequestDate::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(Contract::class, ['id' => 'contract_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Contractor::class, ['id' => 'contractor_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContractType()
    {
        return $this->hasOne(ContractType::class, ['id' => 'contract_type_id']);
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'requestDates' => RequestDate::class,
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function getTablePartColumns($tablePartRelation, $form, $readonly = false)
    {
        /** @var ActiveForm $form */
        $model = $this;
        $parentResult = DocumentController::getTablePartColumns($model, $tablePartRelation, $form, $readonly);
        if ($tablePartRelation == 'requestDates') {
            // Колонка продукты
            $parentResult['products'] = [
                'format' => 'raw',
                'label' => 'Продукты',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model) {
                    /** @var RequestDate $rowModel */
                    $result = '';
                    if (!$rowModel->isNewRecord && isset($rowModel->requestDateProducts)) {

                        $result .= '<table class="table table-bordered">';
                        $result .= '<tr>';
                        $result .= '<th>Продукт</th>';
                        $result .= '<th>Единица измерения</th>';
                        $result .= '<th>Планируемое количество</th>';
                        $result .= '<th>Фактическое количество</th>';
                        $result .= '</tr>';
                        foreach ($rowModel->requestDateProducts as $requestDateProduct) {
                            $result .= '<tr>';
                            $result .= '<th>'.$requestDateProduct->product.'</th>';
                            $result .= '<th>'.$requestDateProduct->unit.'</th>';
                            $result .= '<th>'.$requestDateProduct->planned_quantity.'</th>';
                            $result .= '<th>'.$requestDateProduct->current_quantity.'</th>';
                            $result .= '</tr>';
                        }
                        $result .= '</table>';
                    }
                    return $result;
                }
            ];
        }
        return $parentResult;
    }
}
