<?php

namespace common\models\enum;

use common\components\import\ImportContractorAndContract;

class ConsoleTaskType extends Enum
{
    /**
     * Импорт контрагентов и договоров
     */
    const IMPORT_CONTRACTOR_AND_CONTRACT = 1;

    /**
     * Получение имени класса обработчика задачи в зависимости от типа задачи
     * @param integer $taskTypeId тип задачи
     * @return string (false если класс не указан)
     */
    static public function getTaskProcessorClassByTypeId($taskTypeId)
    {
        switch ($taskTypeId) {
            case self::IMPORT_CONTRACTOR_AND_CONTRACT:
                return ImportContractorAndContract::className();
        }
        return false;
    }
}