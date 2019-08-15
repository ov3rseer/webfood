<?php

use common\components\pgsql\Migration;

class m130710_113758_add_rbac_entities extends Migration
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function up()
    {
        $this->createTable('{{%sys_auth_rule}}', [
            'name'       => $this->string(255)->notNull(),
            'data'       => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ]);

        $this->createTable('{{%sys_auth_item}}', [
            'name'        => $this->string(255)->notNull(),
            'type'        => $this->smallInteger()->notNull()->indexed(),
            'description' => $this->text(),
            'rule_name'   => $this->string(255),
            'data'        => $this->binary(),
            'created_at'  => $this->integer(),
            'updated_at'  => $this->integer(),
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES {{%sys_auth_rule}} (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%sys_auth_item_child}}', [
            'parent' => $this->string(255)->notNull(),
            'child'  => $this->string(255)->notNull(),
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES {{%sys_auth_item}} (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES {{%sys_auth_item}} (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%sys_auth_assignment}}', [
            'item_name'  => $this->string(255)->notNull(),
            'user_id'    => $this->string(255)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES {{%sys_auth_item}} (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        // Добавляем роль суперадмина
        $auth = Yii::$app->authManager;
        $role = $auth->createRole('super-admin');
        $role->description = 'Супер-админ';
        $auth->add($role);
        // Добавляем админа
        $auth->assign($role, 1);

        // Добавляем роль объекта обслуживания
        $role = $auth->createRole('service-object');
        $role->description = 'Объект обслуживания';
        $auth->add($role);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%sys_auth_assignment}}');
        $this->dropTable('{{%sys_auth_item_child}}');
        $this->dropTable('{{%sys_auth_item}}');
        $this->dropTable('{{%sys_auth_rule}}');
    }
}
