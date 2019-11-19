<?php

use common\models\enum\ContractType;

$date_from =  new DateTime('now');
$date_to =  clone $date_from;
$date_to->modify('+ 1 year');

return [
    'contract-1' => [
        'name' => 'Контракт со школой №23',
        'contract_code' => 1,
        'contract_type_id' => ContractType::CHILD,
        'date_from' => $date_from->format('Y-m-d'),
        'date_to' => $date_to->format('Y-m-d'),
    ]
];