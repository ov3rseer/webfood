<?php

use common\components\pgsql\Migration;

class m191129_122208_rename_console_task extends Migration
{
    public function safeUp()
    {
        $this->update('{{%enum_console_task_type}}', ['name' => 'Импорт объектов обслуживания'], ['id' => 1]);
    }

    public function safeDown()
    {
        $this->update('{{%enum_console_task_type}}', ['name' => 'Импорт объектов обслуживания и договоров'], ['id' => 1]);
    }
}