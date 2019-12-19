<?php


namespace common\models\enum;


class RequestStatus extends Enum
{
    /**
     * Новая
     */
    const NEW = 1;

    /**
     * Забронировано
     */
    const RESERVED = 2;

    /**
     * В пути
     */
    const IN_ROUTE = 3;

    /**
     * Доставлено
     */
    const DELIVERED = 4;
}