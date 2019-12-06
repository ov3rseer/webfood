<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m191129_133528_delete_contracts extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->dropColumn('{{%doc_request}}', 'service_object_code');
        $this->dropColumn('{{%doc_request}}', 'contract_code');
        $this->dropColumn('{{%doc_request}}', 'address');
        $this->dropColumn('{{%doc_request}}', 'contract_id');
        $this->addColumn('{{%ref_service_object}}', 'city', $this->string(128)->notNull());
        $this->addColumn('{{%ref_service_object}}', 'zip_code', $this->integer(6)->notNull());
        $this->addColumn('{{%ref_service_object}}', 'address', $this->string(256)->notNull());
        $this->dropColumn('{{%ref_service_object}}', 'service_object_code');
        $this->dropTable('{{%tab_service_object_contract}}');
        $this->dropTable('{{%tab_contract_product}}');
        $this->dropTable('{{%ref_contract}}');
        $this->dropTable('{{%enum_contract_type}}');
        $this->addColumn('{{%ref_product_provider}}', 'city', $this->string(128)->notNull());
        $this->addColumn('{{%ref_product_provider}}', 'zip_code', $this->integer(6)->notNull());
        $this->addColumn('{{%ref_product_provider}}', 'address', $this->string(256)->notNull());
    }

    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->dropColumn('{{%ref_product_provider}}', 'city');
        $this->dropColumn('{{%ref_product_provider}}', 'zip_code');
        $this->dropColumn('{{%ref_product_provider}}', 'address');

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

        $this->addColumn('{{%ref_service_object}}', 'service_object_code', $this->string(9)->unique());
        $this->dropColumn('{{%ref_service_object}}', 'address');
        $this->dropColumn('{{%ref_service_object}}', 'zip_code');
        $this->dropColumn('{{%ref_service_object}}', 'city');

        $this->addColumn('{{%doc_request}}', 'contract_id',
            $this->integer()->notNull()->indexed()->foreignKey('{{%ref_contract}}', 'id'));
        $this->addColumn('{{%doc_request}}', 'address', $this->string(9));
        $this->addColumn('{{%doc_request}}', 'contract_code', $this->string(9));
        $this->addColumn('{{%doc_request}}', 'service_object_code', $this->string(9));
    }
}