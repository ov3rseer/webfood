<?php

use common\components\pgsql\Migration;

class m190807_095706_add_ref_school_class extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_school_class}}', [
            'number' => $this->integer(2)->notNull(),
            'litter' => $this->char(1)->notNull(),
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\SchoolClass']);

        $this->createTablePartTable('{{%tab_service_object_school_class}}','{{%ref_service_object}}',[
            'school_class_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_school_class}}', 'id'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_service_object_school_class}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\SchoolClass']);
        $this->dropTable('{{%ref_school_class}}');
    }
}