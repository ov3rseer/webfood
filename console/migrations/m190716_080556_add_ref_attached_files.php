<?php

use common\components\mysql\Migration;

class m190716_080556_add_ref_attached_files extends Migration
{
    private $_permissionsForFiles;

    /**
     * @param array $config
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForFiles = $this->getPermissions('backend\controllers\reference\FileController', 'Файлы', 46);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeUp()
    {
        // Создание таблицы для моделей "Файл"

        $this->createReferenceTable('{{%ref_file}}', [
            'name_full' => $this->string(1024)->notNull()->indexed(),
            'path'      => $this->string(256)->notNull(),
            'extension' => $this->string(10)->notNull(),
            'comment'   => $this->text(),
        ]);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForFiles
        );
        $this->addPermissions($permissionForAdd);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->setPermissions();
        $permissionForDelete = array_merge(
            $this->_permissionsForFiles
        );
        $this->deletePermissions($permissionForDelete);

        $this->dropTable('{{%ref_file}}');
    }
}