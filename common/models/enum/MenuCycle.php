<?php

namespace common\models\enum;

/**
 * Чикличность меню
 */
class MenuCycle extends Enum
{
    /**
     * Еженедельно
     */
    const WEEKLY = 1;

    /**
     * Нечётные недели
     */
    const ODD_WEEKS = 2;

    /**
     * Чётные недели
     */
    const EVEN_WEEKS = 3;
}