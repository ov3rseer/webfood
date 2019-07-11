<?php

use common\components\mysql\Migration;
use yii\rbac\Permission;

class m190124_110200_add_verification_token_column_to_user_table extends Migration
{
    private $_permissionsForUser = [
        [
            'name' => 'backend\controllers\reference\UserController.Index',
            'description' => 'Пользователи: Журнал',
        ],
        [
            'name' => 'backend\controllers\reference\UserController.View',
            'description' => 'Пользователи: Просмотр',
        ],
        [
            'name' => 'backend\controllers\reference\UserController.Create',
            'description' => 'Пользователи: Создать',
        ],
        [
            'name' => 'backend\controllers\reference\UserController.Update',
            'description' => 'Пользователи: Изменить',
        ],
        [
            'name' => 'backend\controllers\reference\UserController.Delete',
            'description' => 'Пользователи: Удалить',
        ],
        [
            'name' => 'backend\controllers\reference\UserController.Restore',
            'description' => 'Пользователи: Восстановить',
        ]];

    private $_permissionsForRole = [
        [
            'name' => 'backend\controllers\system\RoleController.Index',
            'description' => 'Роли: Журнал',
        ],
        [
            'name' => 'backend\controllers\system\RoleController.View',
            'description' => 'Роли: Просмотр',
        ],
        [
            'name' => 'backend\controllers\system\RoleController.Create',
            'description' => 'Роли: Создать',
        ],
        [
            'name' => 'backend\controllers\system\RoleController.Update',
            'description' => 'Роли: Изменить',
        ],
        [
            'name' => 'backend\controllers\system\RoleController.Delete',
            'description' => 'Роли: Удалить',
        ],
        [
            'name' => 'backend\controllers\system\RoleController.Restore',
            'description' => 'Роли: Восстановить',
        ]
    ];

    /**
     * @return bool|void
     * @throws Exception
     */
    public function up()
    {
        $permissionForAdd = array_merge(
            $this->_permissionsForUser,
            $this->_permissionsForRole
        );
        $auth = Yii::$app->authManager;
        foreach ($permissionForAdd as $permissionData) {
            $permission = new Permission($permissionData);
            $auth->add($permission);
        }

        $this->addColumn('{{%ref_user}}', 'verification_token', $this->string()->defaultValue(null));
    }

    /**
     * @return bool|void
     */
    public function down()
    {
        $permissionForDelete = array_merge(
            $this->_permissionsForUser,
            $this->_permissionsForRole
        );
        $auth = Yii::$app->authManager;
        foreach ($permissionForDelete as $permissionData) {
            $permission = $auth->getPermission($permissionData['name']);
            if ($permission) {
                $auth->remove($permission);
            }
        }

        $this->dropColumn('{{%ref_user}}', 'verification_token');
    }
}