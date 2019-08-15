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
     * Прочее
     */
    const OTHER = 2;

    /**
     * Объект обслуживания
     */
    const SERVICE_OBJECT = 3;

    /**
     * Сотрудник объекта обслуживания
     */
    const EMPLOYEE = 4;

    /**
     * Родитель
     */
    const FATHER = 5;

    /**
     * Поставщик продуктов
     */
    const PRODUCT_PROVIDER = 6;
}
