<?php

use common\components\mysql\Migration;

class m190709_130259_add_enum_document_status extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{%enum_document_status}}', [
            1 => 'Черновик',
            2 => 'Проведен',
            3 => 'Помечен на удаление',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%enum_document_status}}');
    }
}