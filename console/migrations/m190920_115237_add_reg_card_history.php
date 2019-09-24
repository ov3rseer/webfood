<?php

use common\components\pgsql\Migration;

class m190920_115237_add_reg_card_history extends Migration
{
    public function safeUp()
    {
        $this->createRegisterTableWithDocumentBasis('{{%reg_card_history}}', [
            'card_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_card_child}}', 'id'),
            'sum' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%reg_card_history}}');
    }
}