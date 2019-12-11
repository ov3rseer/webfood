<?php

use common\components\pgsql\Migration;

class m191210_120142_add_doc_arrival extends Migration
{
    public function safeUp()
    {
        $this->createDocumentTable('{{doc_arrival}}', [
            'product_provider_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product_provider}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\Arrival']);

        $this->createTablePartTable('{{%tab_arrival_product}}', '{{doc_arrival}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{ref_product}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%tab_arrival_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\Arrival']);
        $this->dropTable('{{%doc_arrival}}');
    }
}