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

        $this->createTablePartTable('{{%tab_menu_complex}}','{{%ref_menu}}',[
            'complex_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_menu_complex}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Menu']);
        $this->dropTable('{{%ref_menu}}');
    }
}