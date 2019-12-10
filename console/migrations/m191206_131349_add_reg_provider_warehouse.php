<?php

use common\components\pgsql\Migration;

class m191206_131349_add_reg_provider_warehouse extends Migration
{
    public function safeUp()
    {
        $this->createRegisterTableWithDocumentBasis('{{%reg_provider_warehouse}}', [
            'provider_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_product_provider}}', 'id'),
            'product_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_product}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%reg_provider_warehouse}}');
    }
}

