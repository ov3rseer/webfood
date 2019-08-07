<?php

use common\components\pgsql\Migration;
use yii\db\Query;

class m190806_114634_rename_contractor_to_service_object extends Migration
{
    private $_userTypesToRename = [
        2 => [
            'old' => 'Контрагент',
            'new' => 'Объект обслуживания'
        ]
    ];

    private $_permissionsForContractor;
    private $_permissionsForServiceObject;
    private $_permissionsForImportContractorAndContract;
    private $_permissionsForImportServiceObjectAndContract;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForContractor = $this->getPermissions('backend\controllers\reference\ContractorController', 'Контрагенты', 63);
        $this->_permissionsForServiceObject = $this->getPermissions('backend\controllers\reference\ServiceObjectController', 'Объект обслуживания', 63);
        $this->_permissionsForImportContractorAndContract = $this->getPermissions('backend\controllers\system\ImportContractorAndContractController', 'Импорт контрагентов и договоров', 32);
        $this->_permissionsForImportServiceObjectAndContract = $this->getPermissions('backend\controllers\system\ImportServiceObjectAndContractController', 'Импорт объектов обслуживания и договоров', 32);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        foreach ($this->_userTypesToRename as $id => $names) {
            $this->update('{{%enum_user_type}}', ['name' => $names['new']], ['id' => $id]);
        }

        $this->update('{{%enum_console_task_type}}', ['name' => 'Импорт объектов обслуживания и договоров'], ['id' => 1]);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Contractor']);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\ServiceObject']);

        $this->renameTable('{{%ref_contractor}}', '{{%ref_service_object}}');
        $this->renameColumn('{{%ref_service_object}}', 'contractor_code', 'service_object_code');

        $this->renameColumn('{{%doc_request}}', 'contractor_code', 'service_object_code');
        $this->renameColumn('{{%doc_request}}', 'contractor_id', 'service_object_id');

        $this->renameTable('{{%tab_contractor_contract}}', '{{%tab_service_object_contract}}');

        $this->createEnumTable('{{%enum_service_object_type}}',[
            1 => 'Прочее',
            2 => 'Детский сад',
            3 => 'Школа',
        ]);
        $this->addColumn('{{%ref_service_object}}', 'service_object_type_id', $this->integer()->indexed()->foreignKey('{{%enum_service_object_type}}','id'));

        $serviceObjectIds = (new Query())
            ->select('id')
            ->from('{{%ref_service_object}}')
            ->column();
        $serviceObjectTypeId = (new Query())
            ->select('id')
            ->from('{{%enum_service_object_type}}')
            ->andWhere(['LIKE', 'name', 'Детский'])
            ->scalar();
        $this->update('{{%ref_service_object}}', ['service_object_type_id' => $serviceObjectTypeId], ['id' => $serviceObjectIds]);
        $this->alterColumn('{{%ref_service_object}}', 'service_object_type_id', 'SET NOT NULL');

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForServiceObject,
            $this->_permissionsForImportServiceObjectAndContract
        );
        $this->addPermissions($permissionForAdd);
        $permissionForDelete = array_merge(
            $this->_permissionsForContractor,
            $this->_permissionsForImportContractorAndContract
        );
        $this->deletePermissions($permissionForDelete);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForContractor,
            $this->_permissionsForImportContractorAndContract
        );
        $this->addPermissions($permissionForAdd);
        $permissionForDelete = array_merge(
            $this->_permissionsForServiceObject,
            $this->_permissionsForImportServiceObjectAndContract
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropColumn('{{%ref_service_object}}', 'service_object_type_id');
        $this->dropTable('{{%enum_service_object_type}}');

        $this->renameTable('{{%tab_service_object_contract}}', '{{%tab_contractor_contract}}');

        $this->renameColumn('{{%doc_request}}', 'service_object_code', 'contractor_code');
        $this->renameColumn('{{%doc_request}}', 'service_object_id', 'contractor_id');

        $this->renameColumn('{{%ref_service_object}}', 'service_object_code', 'contractor_code');
        $this->renameTable('{{%ref_service_object}}', '{{%ref_contractor}}');

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\ServiceObject']);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Contractor']);

        $this->update('{{%enum_console_task_type}}', ['name' => 'Импорт контрагентов и договоров'], ['id' => 1]);

        foreach ($this->_userTypesToRename as $id => $names) {
            $this->update('{{%enum_user_type}}', ['name' => $names['old']], ['id' => $id]);
        }
    }
}