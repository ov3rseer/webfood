<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190712_072430_add_ref_service_object extends Migration
{
    private $_userTypes = [
        3 => 'Объект обслуживания'
    ];

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{%enum_service_object_type}}', [
            1 => 'Прочее',
            2 => 'Детский сад',
            3 => 'Школа',
        ]);

        $rows = [];
        foreach ($this->_userTypes as $id => $name) {
            $rows[] = ['id' => $id, 'name' => $name];
        }
        $this->batchInsert('{{%enum_user_type}}', ['id', 'name'], $rows);
        $this->resetSequence('{{%enum_user_type}}');

        $this->createReferenceTable('{{%ref_service_object}}', [
            'user_id' => $this->integer()->indexed()->foreignKey('{{%ref_user}}', 'id'),
            'service_object_type_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%enum_service_object_type}}', 'id'),
            'city' => $this->string(128)->notNull(),
            'zip_code' => $this->integer(6)->notNull(),
            'address' => $this->string(256)->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ServiceObject']);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();

        $this->delete('{{%ref_service_object}}', ['user_id' => $userIds]);
        $this->update('{{%ref_user}}', ['user_type_id' => 2, 'is_active' => false], ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ServiceObject']);
        $this->dropTable('{{%ref_service_object}}');
        $this->dropTable('{{%enum_service_object_type}}');
    }
}