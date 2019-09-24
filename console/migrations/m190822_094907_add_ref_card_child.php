<?php

use common\components\pgsql\Migration;

class m190822_094907_add_ref_card_child extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_card_child}}', [
            'card_number' => $this->string()->indexed()->notNull()->unique(),
            'balance' => $this->decimal(10, 2)->defaultValue(0)->notNull(),
            'limit_per_day' => $this->decimal(10, 2)->defaultValue(0)->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\CardChild']);
        $this->addColumn('{{%ref_child}}', 'card_id', $this->integer()->indexed()->foreignKey('{{%ref_card_child}}', 'id'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ref_child}}', 'card_id');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\CardChild']);
        $this->dropTable('{{%ref_card_child}}');
    }
}