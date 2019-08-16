<?php

use common\components\pgsql\Migration;

class m190716_080556_add_ref_attached_files extends Migration
{
    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        // Создание таблицы для моделей "Файл"

        $this->createReferenceTable('{{%ref_file}}', [
            'path'      => $this->string(256)->notNull(),
            'extension' => $this->string(10)->notNull(),
            'comment'   => $this->text(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\File']);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\File']);
        $this->dropTable('{{%ref_file}}');
    }
}