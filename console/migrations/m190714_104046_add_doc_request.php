<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190714_104046_add_doc_request extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{enum_request_status}}', [
            1 => 'Новая',
            2 => 'Забронировано',
            3 => 'В пути',
            4 => 'Доставлено',
        ]);

        $this->createDocumentTable('{{%doc_request}}', [
            'delivery_day' => $this->date()->notNull(),
            'service_object_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_service_object}}', 'id'),
            'request_status_id' => $this->integer()->notNull()->indexed()->foreignKey('{{enum_request_status}}', 'id'),
        ]);

        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\Request']);

        $this->createTablePartTable('{{%tab_request_product}}', '{{%doc_request}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'quantity' => $this->float()->notNull(),
        ]);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_request_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\Request']);
        $this->dropTable('{{%doc_request}}');
        $this->dropTable('{{%enum_request_table}}');
    }
}