<?php

use common\components\pgsql\Migration;

class m190807_082518_add_ref_meal extends Migration
{
    private $_permissionsForMeal;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForMeal = $this->getPermissions('backend\controllers\reference\MealController', 'Блюда', 63);
    }

    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_meal}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Meal']);

        $this->createTablePartTable('{{%tab_meal_product}}','{{%ref_meal}}',[
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_unit}}','id'),
            'product_quantity' => $this->decimal(10,2)->notNull(),
        ]);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForMeal
        );
        $this->addPermissions($permissionForAdd);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->setPermissions();
        $permissionForDelete = array_merge(
            $this->_permissionsForMeal
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropTable('{{%tab_meal_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Meal']);
        $this->dropTable('{{%ref_meal}}');
    }
}