<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190716_101111_add_import_contractor_and_contract extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->insert('{{%enum_console_task_type}}', [
            'id'   => 1,
            'name' => 'Импорт контрагентов и договоров',
        ]);
        $this->resetSequence('{{%enum_console_task_type}}');
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->delete('{{%ref_console_task}}', ['type_id' => 1]);
        $this->delete('{{%enum_console_task_type}}', ['id' => 1]);
    }
}