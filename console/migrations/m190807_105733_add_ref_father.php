<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190807_105733_add_ref_father extends Migration
{
    private $_userTypes = [
        4 => 'Родитель'
    ];

    private $_permissionsForFather;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForFather = $this->getPermissions('backend\controllers\reference\FatherController', 'Родители', 63);
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

        $this->createReferenceTable('{{%ref_father}}', [
            'forename' => $this->string(256),
            'surname' => $this->string(256),
            'patronymic' => $this->string(256),
            'user_id' => $this->integer()->indexed()->foreignKey('{{%ref_user}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Father']);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForFather
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
            $this->_permissionsForFather
        );
        $this->deletePermissions($permissionForDelete);

        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();
        $this->delete('{{%ref_father}}', ['user_id' => $userIds]);
        $this->update('{{%ref_user}}', ['user_type_id' => null, 'is_active' => false], ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Father']);
        $this->dropTable('{{%ref_father}}');
    }
}