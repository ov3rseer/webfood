<?php

use common\components\mysql\Migration;

class m130524_201442_init extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function up()
    {
        $this->createEnumTable('{{%enum_user_type}}', [
            1 => 'Администратор',
        ]);

        $this->createReferenceTable('{{%ref_user}}', [
            'name_full' => $this->string(1024)->notNull()->indexed(),
            'forename' => $this->string(256),
            'surname' => $this->string(256),
            'user_type_id'  => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_user_type}}', 'id'),
            'email' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
        ]);

        $this->createSystemTable('{{%sys_entity}}', [
            'class_name' => $this->string()->notNull()
        ]);

        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\User']);

        $this->insert('{{%ref_user}}', [
            'id'            => 1,
            'name'          => 'admin',
            'name_full'     => 'Администратор',
            'forename'      => '',
            'surname'       => '',
            'user_type_id'  => 1,
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'email'         => 'admin@example.com',
        ]);
        $this->resetSequence('{{%ref_user}}');

    }

    public function down()
    {
        $this->delete('{{%ref_user}}', ['id' => 1]);
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\User']);
        $this->dropTable('{{%sys_entity}}');
        $this->dropTable('{{%ref_user}}');
        $this->dropTable('{{%enum_user_type}}');
    }
}
