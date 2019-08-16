<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190807_105733_add_ref_father extends Migration
{
    private $_userTypes = [
        5 => 'Родитель'
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

        $this->createReferenceTable('{{%ref_father}}', [
            'forename' => $this->string(256),
            'surname' => $this->string(256),
            'patronymic' => $this->string(256),
            'user_id' => $this->integer()->indexed()->foreignKey('{{%ref_user}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Father']);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();
        $this->delete('{{%ref_father}}', ['user_id' => $userIds]);
        $this->update('{{%ref_user}}', ['user_type_id' => 2, 'is_active' => false], ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Father']);
        $this->dropTable('{{%ref_father}}');
    }
}