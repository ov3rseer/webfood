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
     * Объект обслуживания
     */
    const SERVICE_OBJECT = 2;

    /**
     * Сотрудник объекта обслуживания
     */
    const EMPLOYEE = 3;

    /**
     * Родитель
     */
    const FATHER = 4;

    /**
     * Поставщик продуктов
     */
    const PRODUCT_PROVIDER = 5;
}
