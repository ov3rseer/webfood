<?php

use common\components\pgsql\Migration;

class m190807_084045_add_ref_complex extends Migration
{
    private $_permissionsForComplex;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForComplex = $this->getPermissions('backend\controllers\reference\ComplexController', 'Комплекс', 63);
    }

    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{%enum_complex_type}}', [
            1 => 'Завтрак',
            2 => 'Обед',
            3 => 'Полдник',
            4 => 'Ужин',
        ]);

        $this->createReferenceTable('{{%ref_complex}}',[
            'complex_type_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_complex_type}}', 'id'),
        ]);

        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Complex']);

        $this->createTablePartTable('{{%tab_complex_meal}}','{{%ref_complex}}',[
            'meal_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_meal}}', 'id'),
            'meal_quantity' => $this->decimal(10,2)->notNull(),
        ]);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForComplex
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
            $this->_permissionsForComplex
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropTable('{{%tab_complex_meal}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Complex']);
        $this->dropTable('{{%ref_complex}}');
        $this->dropTable('{{%enum_complex_type}}');
    }
}