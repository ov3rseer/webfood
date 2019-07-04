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
}
