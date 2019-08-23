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
            'card_number_hash' => $this->string()->notNull()->unique(),
            'card_keyword_hash' => $this->string()->defaultValue(null), // заглушка для аутентификации в будущем
            'balance' => $this->float()->defaultValue(0),
            'limit_per_day' => $this->float()->defaultValue(0),
            'child_id' => $this->integer()->indexed()->foreignKey('{{%ref_child}}', 'id'),
            'auth_key' => $this->string(32),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\CardChild']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%ref_card_child}}');
    }
}