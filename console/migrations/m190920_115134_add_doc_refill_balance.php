<?php

use common\components\pgsql\Migration;

class m190920_115134_add_doc_refill_balance extends Migration
{
    public function safeUp()
    {
        $this->createDocumentTable('{{doc_refill_balance}}', [
            'card_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_card_child}}', 'id'),
            'sum' => $this->decimal(10, 2)->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\RefillBalance']);
    }

    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\RefillBalance']);
        $this->dropTable('{{%doc_refill_balance}}');
    }
}