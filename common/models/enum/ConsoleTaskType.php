<?php

namespace common\models\enum;

use common\components\import\ImportProductProvider;
use common\components\import\ImportServiceObject;

class ConsoleTaskType extends Enum
{
    /**
     * Импорт объектов обслживания
     */
    const IMPORT_SERVICE_OBJECT = 1;

    /**
     * Импорт поставщиков продуктов
     */
    const IMPORT_PRODUCT_PROVIDER = 2;

    /**
     * Получение имени класса обработчика задачи в зависимости от типа задачи
     * @param integer $taskTypeId тип задачи
     * @return string (false если класс не указан)
     */
    static public function getTaskProcessorClassByTypeId($taskTypeId)
    {
        switch ($taskTypeId) {
            case self::IMPORT_SERVICE_OBJECT:
                return ImportServiceObject::class;
            case self::IMPORT_PRODUCT_PROVIDER:
                return ImportProductProvider::class;
        }
        return false;
    }
}