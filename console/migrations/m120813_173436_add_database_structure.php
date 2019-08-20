<?php

use common\components\pgsql\Migration;

class m120813_173436_add_database_structure extends Migration
{
    public function safeUp()
    {
        /*$this->createSystemTable('{{%sys_structure}}', [
            'structure' => $this->string(65536), // 256 KiB вроде
        ]);
        $this->insert('{{%sys_structure}}', [
            'id' => 1,
            'structure' => '[]',
        ]);*/
    }

    public function safeDown()
    {
        /*$this->dropTable('{{%sys_structure}}');*/
    }
}