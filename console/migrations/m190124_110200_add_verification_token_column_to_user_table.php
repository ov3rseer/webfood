<?php

use common\components\pgsql\Migration;

class m190124_110200_add_verification_token_column_to_user_table extends Migration
{
    private $_permissionsForUser;
    private $_permissionsForRole;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForUser = $this->getPermissions('backend\controllers\reference\UserController', 'Пользователи', 63);
        $this->_permissionsForRole = $this->getPermissions('backend\controllers\system\RoleController', 'Роли', 46);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function up()
    {
        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForUser,
            $this->_permissionsForRole
        );
        $this->addPermissions($permissionForAdd);

        $this->addColumn('{{%ref_user}}', 'verification_token', $this->string()->defaultValue(null));
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function down()
    {
        $this->setPermissions();
        $permissionForDelete = array_merge(
            $this->_permissionsForUser,
            $this->_permissionsForRole
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropColumn('{{%ref_user}}', 'verification_token');
    }
}