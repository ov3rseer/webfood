<?php

namespace common\models\enum;

/**
 * Тип заявки
 */
class UserType extends Enum
{
    /**
     * Администратор
     */
    const ADMIN = 1;

    /**
     * Контрагент
     */
    const CONTRACTOR = 2;
}
