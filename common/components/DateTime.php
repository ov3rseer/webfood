<?php

namespace common\components;

use DateTime as BaseDateTime;
use DateTimeZone;
use Exception;

class DateTime extends BaseDateTime
{
    /**
     * SQL формат строки без времени
     */
    const DB_DATE_FORMAT = 'Y-m-d';

    /**
     * SQL формат строки с временем
     */
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var bool формат приведения объекта к строке (с временем или без)
     */
    public $withTime = true;

    /**
     * Конструктор класса
     * @param string $time
     * @param bool $withTime
     * @param DateTimeZone $timezone
     * @throws Exception
     */
    public function __construct($time = 'now', $withTime = true, DateTimeZone $timezone = null)
    {
        parent::__construct($time, $timezone);
        $this->withTime = $withTime;
    }

    /**
     * Магическая функция привидения объекта к строковому представлению
     * @return string
     */
    public function __toString()
    {
        return (string)$this->format($this->withTime ? self::DB_DATETIME_FORMAT : self::DB_DATE_FORMAT);
    }
}
