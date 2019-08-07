<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190716_101111_add_import_contractor_and_contract extends Migration
{
    private $_permissionsForImportContractorAndContract;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForImportContractorAndContract = $this->getPermissions('backend\controllers\system\ImportContractorAndContractController', 'Импорт контрагентов и договоров', 32);
    }

    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->insert('{{%enum_console_task_type}}', [
            'id'   => 1,
            'name' => 'Импорт контрагентов и договоров',
        ]);
        $this->resetSequence('{{%enum_console_task_type}}');

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForImportContractorAndContract
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
            $this->_permissionsForImportContractorAndContract
        );
        $this->deletePermissions($permissionForDelete);

        $this->delete('{{%ref_console_task}}', ['type_id' => 1]);
        $this->delete('{{%enum_console_task_type}}', ['id' => 1]);
    }
}