<?php

namespace frontend\models\request;

use backend\widgets\ActiveField;
use frontend\models\FrontendForm;

/*
 * @property array $contractor_name
 * @property array $contract_code
 * @property array $address
 * @property array $logic
 */

class RequestForm extends FrontendForm
{
    public $contractor_name;
    //public $contractor_code;
    public $contract_code;
    //public $address;
    public $logic;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['contractor_name', /*'contractor_code',*/ 'contract_code', /*'address'*/], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'contractor_name' => 'Код заказчик: заказчик',
            //'contractor_code' => 'Код заказчика',
            'contract_code'   => 'Код договора: место поставки',
            //'address'         => 'Место поставки',
        ]);
    }
}