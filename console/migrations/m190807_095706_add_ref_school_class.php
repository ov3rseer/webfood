<?php

use common\components\pgsql\Migration;

class m190807_095706_add_ref_school_class extends Migration
{
    private $_permissionsForSchoolClass;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForSchoolClass = $this->getPermissions('backend\controllers\reference\SchoolClassController', 'Классы', 63);
    }

    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_school_class}}', [
            'number' => $this->integer(2)->notNull(),
            'litter' => $this->char(1)->notNull(),
            'teacher_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_employee}}', 'id'),
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\SchoolClass']);

        $this->createTablePartTable('{{%tab_service_object_school_class}}','{{%ref_service_object}}',[
            'school_class_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_school_class}}', 'id'),
        ]);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForSchoolClass
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
            $this->_permissionsForSchoolClass
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropTable('{{%tab_service_object_school_class}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\SchoolClass']);
        $this->dropTable('{{%ref_school_class}}');
    }
}