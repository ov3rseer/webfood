<?php

use common\components\pgsql\Migration;

class m190920_115053_add_doc_purchase extends Migration
{
    public function safeUp()
    {
        $this->createDocumentTable('{{doc_purchase}}', [
            'card_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_card_child}}', 'id'),
            'sum' => $this->decimal(10, 2)->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\Purchase']);

        $this->createTablePartTable('{{%tab_purchase_meal}}', '{{doc_purchase}}', [
            'meal_id' => $this->integer()->notNull()->indexed()->foreignKey('{{ref_meal}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
        ]);

        $this->createTablePartTable('{{%tab_purchase_complex}}', '{{doc_purchase}}', [
            'complex_id' => $this->integer()->notNull()->indexed()->foreignKey('{{ref_complex}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%tab_purchase_complex}}');
        $this->dropTable('{{%tab_purchase_meal}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\Purchase']);
        $this->dropTable('{{%doc_purchase}}');
    }
}