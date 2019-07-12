<?php

namespace common\models\enum;

/**
 * Статус заявки
 */
class TypeRequest extends Enum
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
