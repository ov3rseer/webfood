<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190807_093000_add_ref_employee extends Migration
{
    private $_userTypes = [
        4 => 'Сотрудник'
    ];

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
            'user_id' => $this->integer()->indexed()->foreignKey('{{%ref_user}}', 'id'),
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Employee']);

        $this->createTablePartTable('{{%tab_service_object_employee}}','{{%ref_service_object}}',[
            'employee_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_employee}}', 'id'),
        ]);

        // Добавляем роль сотрудника
        $auth = Yii::$app->authManager;
        $role = $auth->createRole('employee');
        $role->description = 'Сотрудник';
        $auth->add($role);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_service_object_employee}}');

        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();
        $this->delete('{{%ref_employee}}', ['user_id' => $userIds]);
        $this->update('{{%ref_user}}', ['user_type_id' => 2, 'is_active' => false], ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Employee']);
        $this->dropTable('{{%ref_employee}}');
    }
}