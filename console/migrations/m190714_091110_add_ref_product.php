<?php

use common\components\pgsql\Migration;

class m190714_091110_add_ref_product extends Migration
{
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_product}}', [
            'price' => $this->decimal(10, 2)->notNull(),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_unit}}', 'id'),
            'product_category_id' => $this->integer()->indexed()->foreignKey('{{%ref_product_category}}', 'id'),
            'product_provider_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_product_provider}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Product']);
    }

    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Product']);
        $this->dropTable('{{%ref_product}}');
    }
}