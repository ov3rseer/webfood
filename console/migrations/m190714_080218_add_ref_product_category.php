<?php

use common\components\pgsql\Migration;

class m190714_080218_add_ref_product_category extends Migration
{
    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_product_category}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductCategory']);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductCategory']);
        $this->dropTable('{{%ref_product_category}}');
    }
}