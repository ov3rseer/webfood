<?php

use common\components\pgsql\Migration;

class m190807_092056_add_ref_menu extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_menu}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Menu']);

        $this->createTablePartTable('{{%tab_menu_meal}}','{{%ref_menu}}',[
            'meal_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_meal}}', 'id'),
        ]);
        $this->createTablePartTable('{{%tab_menu_complex}}','{{%ref_menu}}',[
            'complex_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_complex}}', 'id'),
        ]);

        $this->createDocumentTable('{{%doc_set_menu}}', [
            'menu_id' => $this->integer()->notNull()->indexed()->foreignKey('{{ref_menu}}', 'id'),
            'day' => $this->date()->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\SetMenu']);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\SetMenu']);
        $this->dropTable('{{%doc_set_menu}}');
        $this->dropTable('{{%tab_menu_complex}}');
        $this->dropTable('{{%tab_menu_meal}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Menu']);
        $this->dropTable('{{%ref_menu}}');
    }
}