<?php

use common\components\mysql\Migration;

class m130524_201442_init extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function up()
    {
        $this->createReferenceTable('{{%ref_user}}', [
            'username' => $this->string()->notNull(),
            'surname' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
        ]);

        $this->createSystemTable('{{%sys_entity}}', [
            'class_name' => $this->string()->notNull()
        ]);

        $this->insert('{{%sys_entity%}}', ['class_name' => 'common\models\reference\User']);
    }

    public function down()
    {
        $this->dropTable('{{%ref_user}}');
        $this->dropTable('{{%sys_entity}}');
        $this->delete('{{%sys_entity%}}', ['class_name' => 'common\models\reference\User']);
    }
}
