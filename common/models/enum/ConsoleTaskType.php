<?php

namespace common\models\enum;

use common\components\import\ImportServiceObjectAndContract;

class ConsoleTaskType extends Enum
{
    /**
     * Импорт объектов обслживания и договоров
     */
    const IMPORT_SERVICE_OBJECT_AND_CONTRACT = 1;

    /**
     * Получение имени класса обработчика задачи в зависимости от типа задачи
     * @param integer $taskTypeId тип задачи
     * @return string (false если класс не указан)
     */
    static public function getTaskProcessorClassByTypeId($taskTypeId)
    {
        switch ($taskTypeId) {
            case self::IMPORT_SERVICE_OBJECT_AND_CONTRACT:
                return ImportServiceObjectAndContract::className();
        }
        return false;
    }
}