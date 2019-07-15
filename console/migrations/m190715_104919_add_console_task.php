<?php

use common\components\mysql\Migration;

class m190715_104919_add_console_task extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{%enum_console_task_status}}', [
            1 => 'Запланирована',
            2 => 'Выполняется',
            3 => 'Выполнена',
            4 => 'Отменена',
            5 => 'Прервана',
        ]);

        $this->createEnumTable('{{%enum_console_task_type}}', []);

        $this->createReferenceTable('{{%ref_console_task}}', [
            'type_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_console_task_type}}', 'id'),
            'status_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_console_task_status}}', 'id'),
            'params' => $this->text(),
            'start_date' => $this->timestamp()->notNull(),
            'finish_date' => $this->timestamp(),
            'is_repeatable' => $this->boolean()->notNull()->defaultValue(false),
            'repeat_interval' => $this->integer(),
            'result_text' => $this->text(),
            'result_data' => $this->text(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ref_console_task}}');
        $this->dropTable('{{%enum_console_task_type}}');
        $this->dropTable('{{%enum_console_task_status}}');
    }
}