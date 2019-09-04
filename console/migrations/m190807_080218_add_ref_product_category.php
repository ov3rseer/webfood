<?php

use common\components\pgsql\Migration;

class m190807_080218_add_ref_product_category extends Migration
{
    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_product_category}}',[
            'parent_id' => $this->integer()->indexed()->foreignKey('{{%ref_product_category}}', 'id')
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductCategory']);

        $this->addColumn('{{%ref_product}}', 'product_category_id', $this->integer()->indexed()->foreignKey('{{%ref_product_category}}', 'id'));
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropColumn('{{%ref_product}}', 'product_category_id');

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductCategory']);
        $this->dropTable('{{%ref_product_category}}');
    }
}