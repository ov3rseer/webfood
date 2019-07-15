<?php

namespace common\models\enum;

/**
 * Статус задачи, запускаемой cron'ом
 */
class ConsoleTaskStatus extends Enum
{
    /**
     * Запланирована
     */
    const PLANNED = 1;

    /**
     * Выполняется
     */
    const IN_PROGRESS = 2;

    /**
     * Выполнена
     */
    const FINISHED = 3;

    /**
     * Отменена (пользователем или бизнес-логикой)
     */
    const CANCELLED = 4;

    /**
     * Прервана (из-за какой-либо необработанной ошибки)
     */
    const INTERRUPTED = 5;
}
