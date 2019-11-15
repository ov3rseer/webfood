<?php

$keys = ['card-1', 'card-2', 'card-3', 'card-4', 'card-5'];

foreach ($keys as $key) {
    $cardNumber = '';
    $length = 10;
    for ($i = 0; $i < $length; $i++) {
        $cardNumber .= mt_rand(0, 9);
    }
    $result[$key] = [
        'name' => 'Карта №' . $cardNumber,
        'card_number' => $cardNumber,
        'balance' => 0,
        'limit_per_day' => 0,
    ];
}

return $result;