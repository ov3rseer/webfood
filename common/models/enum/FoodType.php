<?php

namespace common\models\enum;

/**
 * Тип блюда
 */
class FoodType extends Enum
{
    /**
     * Прочее
     */
    const OTHER = 1;

    /**
     * Для буфета
     */
    const BUFFET = 2;

    /**
     * Горячее питание
     */
    const HOT = 3;
}