<?php

use common\components\pgsql\Migration;

class m191129_072430_add_import_product_provider extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->insert('{{%enum_console_task_type}}', [
            'id' => 2,
            'name' => 'Импорт поставщиков продуктов',
        ]);
        $this->resetSequence('{{%enum_console_task_type}}');
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->delete('{{%ref_console_task}}', ['type_id' => 2]);
        $this->delete('{{%enum_console_task_type}}', ['id' => 2]);
    }
}