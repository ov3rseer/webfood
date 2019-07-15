<?php

namespace common\models\enum;

class ConsoleTaskType extends Enum
{
    /**
     * Получение имени класса обработчика задачи в зависимости от типа задачи
     * @param integer $taskTypeId тип задачи
     * @return string (false если класс не указан)
     */
    static public function getTaskProcessorClassByTypeId($taskTypeId)
    {
        switch ($taskTypeId) {
        }
        return false;
    }
}