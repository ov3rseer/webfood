<?php

use common\components\mysql\Migration;
use yii\rbac\Permission;

class m190712_072430_add_ref_contractor extends Migration
{
    private $_permissionsForContract = [
        [
            'name' => 'backend\controllers\reference\ContractController.Index',
            'description' => 'Единицы измерения: Журнал',
        ],
        [
            'name' => 'backend\controllers\reference\ContractController.View',
            'description' => 'Единицы измерения: Просмотр',
        ],
        [
            'name' => 'backend\controllers\reference\ContractController.Create',
            'description' => 'Единицы измерения: Создать',
        ],
        [
            'name' => 'backend\controllers\reference\ContractController.Update',
            'description' => 'Единицы измерения: Изменить',
        ],
        [
            'name' => 'backend\controllers\reference\ContractController.Delete',
            'description' => 'Единицы измерения: Удалить',
        ],
        [
            'name' => 'backend\controllers\reference\ContractController.Restore',
            'description' => 'Единицы измерения: Восстановить',
        ]
    ];

    private $_permissionsForContractor = [
        [
            'name' => 'backend\controllers\reference\ContractorController.Index',
            'description' => 'Единицы измерения: Журнал',
        ],
        [
            'name' => 'backend\controllers\reference\ContractorController.View',
            'description' => 'Единицы измерения: Просмотр',
        ],
        [
            'name' => 'backend\controllers\reference\ContractorController.Create',
            'description' => 'Единицы измерения: Создать',
        ],
        [
            'name' => 'backend\controllers\reference\ContractorController.Update',
            'description' => 'Единицы измерения: Изменить',
        ],
        [
            'name' => 'backend\controllers\reference\ContractorController.Delete',
            'description' => 'Единицы измерения: Удалить',
        ],
        [
            'name' => 'backend\controllers\reference\ContractorController.Restore',
            'description' => 'Единицы измерения: Восстановить',
        ]
    ];

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

        $permissionForAdd = array_merge(
            $this->_permissionsForContract,
            $this->_permissionsForContractor
        );
        $auth = Yii::$app->authManager;
        foreach ($permissionForAdd as $permissionData) {
            $permission = new Permission($permissionData);
            $auth->add($permission);
        }
    }

    public function safeDown()
    {
        $permissionForDelete = array_merge(
            $this->_permissionsForContract,
            $this->_permissionsForContractor
        );
        $auth = Yii::$app->authManager;
        foreach ($permissionForDelete as $permissionData) {
            $permission = $auth->getPermission($permissionData['name']);
            if ($permission) {
                $auth->remove($permission);
            }
        }

        $this->dropTable('{{%ref_contract}}');
        $this->delete('{{%sys_entity%}}', ['class_name' => 'common\models\reference\Contract']);
        $this->dropTable('{{%ref_contractor}}');
        $this->delete('{{%sys_entity%}}', ['class_name' => 'common\models\reference\Contractor']);
    }
}