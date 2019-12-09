<?php

use common\components\pgsql\Migration;

class m190714_104046_add_doc_preliminary_request extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->createDocumentTable('{{%doc_request}}', [
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id')
        ]);

        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\Request']);

        $this->createTablePartTable('{{%tab_request_date}}', '{{%doc_request}}', [
            'week_day_date' => $this->date()->notNull(),
        ]);

        $this->createCrossTable('{{%cross_request_date_product}}', [
            'request_date_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%tab_request_date}}', 'id'),
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'planned_quantity' => $this->float()->notNull(),
            'current_quantity' => $this->float()->notNull(),
        ]);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%cross_request_date_product}}');
        $this->dropTable('{{%tab_request_date}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\Request']);
        $this->dropTable('{{%doc_request}}');
    }
}