<?php

use common\components\pgsql\Migration;

class m190807_111855_add_ref_child extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_card_child}}', [
            'card_number_hash' => $this->string()->notNull(),
            'balance' => $this->float()->defaultValue(0),
        ]);

        $this->createReferenceTable('{{%ref_child}}', [
            'forename' => $this->string(256),
            'surname' => $this->string(256),
            'patronymic' => $this->string(256),
            'father_id' => $this->integer()->indexed()->foreignKey('{{%ref_father}}', 'id'),
            'card_id' => $this->integer()->indexed()->foreignKey('{{%ref_card_child}}', 'id'),
            'school_class_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_school_class}}', 'id'),
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Child']);

        $this->createTablePartTable('{{%tab_father_child}}','{{%ref_father}}',[
            'child_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_child}}', 'id'),
        ]);

        $this->createTablePartTable('{{%tab_school_class_child}}','{{%ref_school_class}}',[
            'child_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_child}}', 'id'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_school_class_child}}');
        $this->dropTable('{{%tab_father_child}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Child']);
        $this->dropTable('{{%ref_child}}');
        $this->dropTable('{{%ref_card_child}}');
    }
}