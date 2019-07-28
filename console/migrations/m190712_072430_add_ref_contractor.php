<?php

use common\components\pgsql\Migration;

class m190712_072430_add_ref_contractor extends Migration
{
    private $_userTypes = [
        2 => 'Контрагент'
    ];

    private $_permissionsForContract;
    private $_permissionsForContractor;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForContract = $this->getPermissions('backend\controllers\reference\ContractController', 'Контракты', 63);
        $this->_permissionsForContractor = $this->getPermissions('backend\controllers\reference\ContractorController', 'Контрагенты', 63);
    }

    /**
     * @return bool|void
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

        $this->createReferenceTable('{{%ref_contractor}}', [
            'contractor_code' => $this->string(9)->notNull()->unique(),
            'user_id' => $this->integer()->indexed()->foreignKey('{{%ref_user}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Contractor']);

        $this->createReferenceTable('{{%ref_contract}}', [
            'contract_code' => $this->string(9)->notNull()->unique(),
            'contract_type_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_contract_type}}', 'id'),
            'date_from' => $this->date(),
            'date_to' => $this->date(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Contract']);

        $this->createTablePartTable('{{%tab_contract_product}}', '{{%ref_contract}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
        ]);

        $this->createTablePartTable('{{%tab_contractor_contract}}', '{{%ref_contractor}}', [
            'contract_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contract}}', 'id'),
            'address' => $this->string()->notNull(),
        ]);

        $this->addColumn('{{%doc_request}}', 'contractor_id', $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contractor}}', 'id'));
        $this->addColumn('{{%doc_request}}', 'contract_id', $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contract}}', 'id'));
        $this->addColumn('{{%doc_request}}', 'address', $this->string()->notNull());
        $this->addColumn('{{%doc_request}}', 'contract_code', $this->string()->notNull());
        $this->addColumn('{{%doc_request}}', 'contractor_code', $this->string()->notNull());
        $this->addColumn('{{%doc_request}}', 'contract_type_id', $this->integer()->notNull()->indexed()->foreignKey('{{%enum_contract_type}}', 'id'));

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForContract,
            $this->_permissionsForContractor
        );
        $this->addPermissions($permissionForAdd);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->setPermissions();
        $permissionForDelete = array_merge(
            $this->_permissionsForContract,
            $this->_permissionsForContractor
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropColumn('{{%doc_request}}', 'contract_type_id');
        $this->dropColumn('{{%doc_request}}', 'contract_id');
        $this->dropColumn('{{%doc_request}}', 'contractor_id');
        $this->dropColumn('{{%doc_request}}', 'contractor_code');
        $this->dropColumn('{{%doc_request}}', 'contract_code');
        $this->dropColumn('{{%doc_request}}', 'address');

        $this->dropTable('{{%tab_contractor_contract}}');
        $this->dropTable('{{%tab_contract_product}}');
        $this->dropTable('{{%ref_contract}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Contract']);
        $this->dropTable('{{%ref_contractor}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Contractor']);

        $this->delete('{{%ref_user}}', ['user_type_id' => array_keys($this->_userTypes)]);
        $this->delete('{{%enum_user_type}}', ['id' => array_keys($this->_userTypes)]);
    }
}