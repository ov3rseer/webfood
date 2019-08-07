<?php

namespace common\models\enum;

/**
 * Тип комплекса
 */
class ComplexType extends Enum
{
    /**
     * Завтрак
     */
    const BREAKFAST = 1;

    /**
     * Обед
     */
    const DINNER = 2;

    /**
     * Полдник
     */
    const BRUNCH = 3;

    /**
     * Ужин
     */
    const SUPPER = 4;
}