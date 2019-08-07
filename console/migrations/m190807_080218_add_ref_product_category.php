<?php

use common\components\pgsql\Migration;

class m190807_080218_add_ref_product_category extends Migration
{
    private $_permissionsForProductCategory;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForProductCategory = $this->getPermissions('backend\controllers\reference\ProductCategory', 'Категории продуктов', 63);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_product_category}}',[
            'plural_name' => $this->string()->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductCategory']);

        $this->addColumn('{{%ref_product}}', 'product_category_id', $this->integer()->indexed()->foreignKey('{{%ref_product_category}}', 'id'));

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForProductCategory
        );
        $this->addPermissions($permissionForAdd);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->setPermissions();
        $permissionForDelete = array_merge(
            $this->_permissionsForProductCategory
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropColumn('{{%ref_product}}', 'product_category_id');

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductCategory']);
        $this->dropTable('{{%ref_product_category}}');
    }
}