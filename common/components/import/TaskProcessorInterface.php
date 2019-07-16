<?php

namespace common\components\import;

use common\models\reference\ConsoleTask;

interface TaskProcessorInterface
{
    /**
     * Обработка задачи
     * @param ConsoleTask $consoleTask
     * @return array (массив с полями "result_text" и "result_data")
     */
    public function processTask($consoleTask);
}
