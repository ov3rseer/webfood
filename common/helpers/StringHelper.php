<?php

namespace common\helpers;

use yii\helpers\StringHelper as YiiStringHelper;

class StringHelper extends YiiStringHelper
{
    /**
     * Генерация случайного уникального ID
     * @return string
     */
    static public function generateFakeId()
    {
        return uniqid('fakeId');
    }

    /**
     * Подсчет вхождений подстрок в строку
     * @param string $string
     * @param string[] $substrings
     * @return integer количество вхождений
     */
    static public function countSubstrings($string, $substrings)
    {
        $result = 0;
        foreach ($substrings as $substring) {
            $result += substr_count($string, $substring);
        }
        return $result;
    }

    /**
     * Очистка строки от управляющих символов
     * @param string $string
     * @return string
     */
    static public function cleanString($string)
    {
        return preg_replace('/[^\PC\s]/u', '', $string);
    }

    /**
     * Генерация случайного пароля указанной длины
     * @param integer $number
     * @return string
     */
    static public function generatePassword($number)
    {
        $chars = [
            'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'R', 'S',
            'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0', '.', ',',
            '!', '?', '&', '%', '@', '*',
            '$', '<', '>', '+', '-', '~',
            '_', '=', '#', ':', ';',
        ];
        $pass = '';
        for ($i = 0; $i < $number; $i++) {
            // Вычисляем случайный индекс массива
            $index = rand(0, count($chars) - 1);
            $pass .= $chars[$index];
        }
        return $pass;
    }

}
