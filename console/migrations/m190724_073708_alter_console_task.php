<?php

use common\components\pgsql\Migration;

class m190724_073708_alter_console_task extends Migration
{
    public function safeUp()
    {
        $this->delete('{{%ref_console_task}}');
        $this->alterColumn('{{%ref_console_task}}', 'repeat_interval', $this->db->schema->createColumnSchemaBuilder($this->db->schema::TYPE_TEXT));
    }

    public function safeDown()
    {
        $this->delete('{{%ref_console_task}}');
        $this->alterColumn(
            '{{%ref_console_task}}',
            'repeat_interval',
            $this->db->schema->createColumnSchemaBuilder($this->db->schema::TYPE_INTEGER)->append('USING repeat_interval::integer')
        );
    }
}