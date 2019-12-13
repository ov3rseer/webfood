<?php

use common\components\pgsql\Migration;

class m191213_124728_add_reg_object_warehouse extends Migration
{
    public function safeUp()
    {
        $this->createRegisterTableWithDocumentBasis('{{%reg_object_warehouse}}', [
            'service_object_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_service_object}}', 'id'),
            'product_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_product}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%reg_object_warehouse}}');
    }
}