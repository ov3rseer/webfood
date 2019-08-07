<?php

namespace common\models\enum;

/**
 * Тип объекта обслуживания
 */
class ServiceObjectType extends Enum
{
    /**
     * Прочее
     */
    const OTHER = 1;

    /**
     * Детский сад
     */
    const KINDERGARTEN = 2;

    /**
     * Школа
     */
    const SCHOOL = 3;
}