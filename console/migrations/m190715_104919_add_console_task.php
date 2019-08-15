<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190715_104919_add_console_task extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_system_setting}}', [
            'data' => $this->text(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\SystemSetting']);

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
            'finish_date' => $this->timestamp()->null(),
            'is_repeatable' => $this->boolean()->notNull()->defaultValue(false),
            'repeat_interval' => $this->integer(),
            'result_text' => $this->text(),
            'result_data' => $this->text(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ConsoleTask']);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ConsoleTask']);
        $this->dropTable('{{%ref_console_task}}');
        $this->dropTable('{{%enum_console_task_type}}');
        $this->dropTable('{{%enum_console_task_status}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\SystemSetting']);
        $this->dropTable('{{%ref_system_setting}}');
    }
}