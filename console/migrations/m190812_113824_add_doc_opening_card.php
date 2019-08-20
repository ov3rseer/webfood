<?php

use common\components\pgsql\Migration;

class m190812_113824_add_doc_opening_card extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createDocumentTable('{{%doc_open_card}}', [
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id')
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\OpenCard']);

        $this->createTablePartTable('{{%tab_open_card_child}}', '{{%doc_open_card}}', [
            'child_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_child}}', 'id'),
            'codeword' => $this->string()->notNull(),
            'snils' => $this->string(11)->notNull(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_open_card_child}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\OpenCard']);
        $this->dropTable('{{%doc_open_card}}');
    }
}