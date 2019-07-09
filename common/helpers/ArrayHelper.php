<?php

namespace common\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Получение заданного количества случайных элементов массива
     * @param array $array
     * @param integer $count
     * @return array
     */
    static public function getRandomItems($array, $count)
    {
        shuffle($array);
        return array_slice($array, 0, $count);
    }

    /**
     * Добавление элемента в массив с сохранением общего количества элементов
     * (при превышении кол-ва элементов удаляются первые элементы)
     * @param array $stack
     * @param mixed $item
     * @param integer $stackSize
     * @return array
     */
    static public function pushToStack($stack, $item, $stackSize)
    {
        array_push($stack, $item);
        return array_slice($stack, -$stackSize);
    }

    /**
     * Сортировка ключей массива в соответствии с указанным порядком
     * @param array $array
     * @param array $keysOrder
     * @return array
     */
    static public function sortByKeysOrder($array, $keysOrder)
    {
        uksort($array, function($key1, $key2) use ($keysOrder) {
            return (array_search($key1, $keysOrder) > array_search($key2, $keysOrder));
        });
        return $array;
    }
}
