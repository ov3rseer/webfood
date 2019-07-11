<?php
namespace common\components;

/**
 * Расширенный класс DB RBAC менеджера
 */
class DbManager extends \yii\rbac\DbManager
{
    const ADMIN_ROLE = 'super-admin';

    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $roles = $this->getRolesByUser($userId);

        if ($roles) {
            foreach ($roles as $role) {
                if ($role->name == self::ADMIN_ROLE) {
                    return true;
                }
            }
        }
        return parent::checkAccess($userId, $permissionName, $params = []);
    }
}