<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190807_122142_add_ref_product_provider extends Migration
{
    private $_userTypes = [
        5 => 'Поставщик продуктов'
    ];

    private $_permissionsForProductProvider;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForProductProvider = $this->getPermissions('backend\controllers\reference\ProductProviderController', 'Поставщик продуктов', 63);
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

        $this->createReferenceTable('{{%ref_product_provider}}', [
            'user_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_user}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductProvider']);

        $this->createTablePartTable('{{%tab_product_provider_service_object}}','{{%ref_product_provider}}',[
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
        ]);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForProductProvider
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
            $this->_permissionsForProductProvider
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropTable('{{%tab_product_provider_service_object}}');

        $userIds = (new Query())
            ->select(['id'])
            ->from('{{%ref_user}}')
            ->andWhere(['user_type_id' => array_keys($this->_userTypes)])
            ->column();
        $this->delete('{{%ref_product_provider}}', ['user_id' => $userIds]);
        $this->delete('{{%ref_user}}', ['id' => $userIds]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ProductProvider']);
        $this->dropTable('{{%ref_product_provider}}');
    }
}