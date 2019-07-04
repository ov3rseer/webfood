<?php

use common\components\Migration;

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
    }

    public function down()
    {
        $this->dropTable('{{%ref_user}}');
    }
}
