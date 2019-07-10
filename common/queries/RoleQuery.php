<?php

namespace common\queries;

use yii\rbac\Role;

/**
 * Класс для работы с запросами к ролям
 * @package common\queries
 */
class RoleQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->andWhere([
            $this->getAlias() . '.type' => Role::TYPE_ROLE,
        ]);
        parent::init();
    }
}
