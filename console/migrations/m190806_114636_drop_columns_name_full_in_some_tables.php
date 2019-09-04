<?php

use common\components\pgsql\Migration;

class m190806_114636_drop_columns_name_full_in_some_tables extends Migration
{
    public $tables = [
        '{{ref_console_task}}',
        '{{ref_contract}}',
        '{{ref_service_object}}',
        '{{ref_file}}',
        '{{ref_product}}'
    ];

    public function safeUp()
    {
        foreach ($this->tables as $table) {
            $currentTable = Yii::$app->db->schema->getTableSchema($table);
            if (isset($currentTable->columns['name_full'])) {
                $this->dropColumn($table, 'name_full');
            }
        }
    }

    public function safeDown()
    {
        foreach ($this->tables as $table) {
            $currentTable = Yii::$app->db->schema->getTableSchema($table);
            if (!isset($currentTable->columns['name_full'])) {
                $this->addColumn($table, 'name_full', $this->string(1024));
            }
        }
    }
}