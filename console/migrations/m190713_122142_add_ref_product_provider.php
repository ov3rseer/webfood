<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190713_122142_add_ref_product_provider extends Migration
{
    private $_userTypes = [
        4 => 'Поставщик продуктов'
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

        $this->createReferenceTable('{{%ref_product_provider}}', [
            'user_id' => $this->integer()->indexed()->foreignKey('{{%ref_user}}', 'id'),
            'city' => $this->string(128)->notNull(),
            'zip_code' => $this->integer(6)->notNull(),
            'address' => $this->string(256)->notNull(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductProvider']);

        $this->createTablePartTable('{{%tab_product_provider_service_object}}','{{%ref_product_provider}}',[
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);

        // Добавляем роль поставщика
        $auth = Yii::$app->authManager;
        $role = $auth->createRole('product-provider');
        $role->description = 'Поставщик продуктов';
        $auth->add($role);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_product_provider_service_object}}');
        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();
        $this->delete('{{%ref_product_provider}}', ['user_id' => $userIds]);
        $this->update('{{%ref_user}}', ['user_type_id' => 2, 'is_active' => false], ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductProvider']);
        $this->dropTable('{{%ref_product_provider}}');
    }
}