<?php

namespace common\models\enum;

/**
 * Тип блюда
 */
class MealType extends Enum
{
    /**
     * Прочее
     */
    const OTHER = 1;

    /**
     * Блюда для буфета
     */
    const BUFFET_MEALS = 2;

    /**
     * Горячее питание
     */
    const HOT_MEALS = 3;
}