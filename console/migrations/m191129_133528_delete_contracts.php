<?php

use common\components\pgsql\Migration;

class m191129_133528_delete_contracts extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->addColumn('{{%ref_service_object}}', 'city', $this->string(128));
        $this->addColumn('{{%ref_service_object}}', 'address', $this->string(256));
        $this->dropColumn('{{%doc_request}}', 'contract_id');
        $this->dropTable('{{%tab_service_object_contract}}');
        $this->dropTable('{{%tab_contract_product}}');
        $this->dropTable('{{%ref_contract}}');
        $this->dropTable('{{%enum_contract_type}}');
    }

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->createEnumTable('{{%enum_contract_type}}', [
            1 => 'Дети',
            2 => 'Сотрудники'
        ]);
        $this->createReferenceTable('{{%ref_contract}}', [
            'contract_code' => $this->string(9)->notNull()->unique(),
            'contract_type_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_contract_type}}', 'id'),
            'date_from' => $this->date(),
            'date_to' => $this->date(),
        ]);
        $this->createTablePartTable('{{%tab_contract_product}}', '{{%ref_contract}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
        ]);
        $this->createTablePartTable('{{%tab_service_object_contract}}', '{{%ref_service_object}}', [
            'contract_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contract}}', 'id'),
            'address' => $this->string()->notNull(),
        ]);
        $this->addColumn('{{%doc_request}}', 'contract_id',
            $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contract}}', 'id'));

        $this->dropColumn('{{%ref_service_object}}', 'address');
        $this->dropColumn('{{%ref_service_object}}', 'city');
    }
}