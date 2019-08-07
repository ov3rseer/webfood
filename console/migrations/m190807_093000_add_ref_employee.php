<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190807_093000_add_ref_employee extends Migration
{
    private $_userTypes = [
        3 => 'Сотрудник'
    ];

    private $_permissionsForEmployee;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForEmployee = $this->getPermissions('backend\controllers\reference\EmployeeController', 'Сотрудники', 63);
    }

    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $rows = [];
        foreach ($this->_userTypes as $id => $name) {
            $rows[] = ['id' => $id, 'name' => $name];
        }
        $this->batchInsert('{{%enum_user_type}}', ['id', 'name'], $rows);
        $this->resetSequence('{{%enum_user_type}}');

        $this->createReferenceTable('{{%ref_employee}}', [
            'forename' => $this->string(256),
            'surname' => $this->string(256),
            'user_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_user}}', 'id'),
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Employee']);

        $this->createTablePartTable('{{%tab_service_object_employee}}','{{%ref_service_object}}',[
            'employee_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_employee}}', 'id'),
        ]);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForEmployee
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
            $this->_permissionsForEmployee
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropTable('{{%tab_service_object_employee}}');

        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();
        $this->delete('{{%ref_employee}}', ['user_id' => $userIds]);
        $this->delete('{{%ref_user}}', ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Employee']);
        $this->dropTable('{{%ref_employee}}');
    }
}