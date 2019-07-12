<?php

use common\components\mysql\Migration;

class m190712_072430_add_ref_contractor extends Migration
{

    private $_permissionsForContract;
    private $_permissionsForContractor;

    /**
     * m190712_072430_add_ref_contractor constructor.
     * @param array $config
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
        $this->createReferenceTable('{{%ref_contractor}}');
        $this->insert('{{%sys_entity%}}', ['class_name' => 'common\models\reference\Contractor']);

        $this->createReferenceTable('{{%ref_contract}}', [
            'contract_number' => $this->integer()->notNull(),
            'date_from' => $this->date(),
            'date_to' => $this->date(),
            'contractor_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contractor}}', 'id'),
        ]);
        $this->insert('{{%sys_entity%}}', ['class_name' => 'common\models\reference\Contract']);

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

        $this->dropTable('{{%ref_contract}}');
        $this->delete('{{%sys_entity%}}', ['class_name' => 'common\models\reference\Contract']);
        $this->dropTable('{{%ref_contractor}}');
        $this->delete('{{%sys_entity%}}', ['class_name' => 'common\models\reference\Contractor']);
    }
}