<?php

use common\components\pgsql\Migration;

class m190812_113824_add_doc_opening_bank_account extends Migration
{
    private $_permissionsForUploadLists;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForUploadLists = $this->getPermissions('frontend\controllers\serviceObject\UploadListsController', 'Загрузка списков', 63);
    }

    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createDocumentTable('{{%doc_open_bank_account}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\OpenBankAccount']);

        $this->createTablePartTable('{{%tab_open_bank_account_child}}', '{{%doc_open_bank_account}}', [
            'child_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_child}}', 'id'),
            'codeword' => $this->string()->notNull(),
            'snils' => $this->string(11)->notNull(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_open_bank_account_child}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\OpenBankAccount']);
        $this->dropTable('{{%doc_open_bank_account}}');
    }
}