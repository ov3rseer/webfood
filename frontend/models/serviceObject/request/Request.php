<?php

namespace frontend\models\serviceObject\request;

use frontend\models\FrontendForm;

/*
 * @property array $service_object_name
 * @property array $service_object_code
 * @property array $contract_code
 * @property array $address
 * @property array $logic
 */

class Request extends FrontendForm
{
    public $service_object_name;
    //public $service_object_code;
    public $contract_code;
    //public $address;
    public $logic;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_name', /*'service_object_code',*/ 'contract_code', /*'address'*/], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object_name' => 'Код заказчика: заказчик',
            //'service_object_code' => 'Код заказчика',
            'contract_code'   => 'Код договора: место поставки',
            //'address'         => 'Место поставки',
        ]);
    }
}