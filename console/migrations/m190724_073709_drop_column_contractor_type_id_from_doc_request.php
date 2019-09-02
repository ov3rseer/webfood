<?php

use common\components\pgsql\Migration;

class m190724_073709_drop_column_contractor_type_id_from_doc_request extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%doc_request}}', 'contract_type_id');
    }

    public function safeDown()
    {
        $this->addColumn('{{%doc_request}}', 'contract_type_id', $this->integer()->indexed()->foreignKey('{{%enum_contract_type}}', 'id'));
        $this->update('{{%doc_request}}', ['contract_type_id' => 1]);
        $this->alterColumn('{{%doc_request}}', 'contract_type_id', 'SET NOT NULL');
    }
}