<?php

namespace common\models\enum;

/**
 * Тип договора с объектом обслуживания
 */
class ContractType extends Enum
{
    /**
     * Дети
     */
    const CHILD = 1;

    /**
     * Сотрудники
     */
    const EMPLOYEES = 2;
}
