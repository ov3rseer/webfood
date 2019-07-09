<?php

namespace common\components\mysql;

/**
 * Расширенный класс миграции
 *
 * @method ColumnSchemaBuilder primaryKey($length = null)
 * @method ColumnSchemaBuilder bigPrimaryKey($length = null)
 * @method ColumnSchemaBuilder string($length = null)
 * @method ColumnSchemaBuilder text()
 * @method ColumnSchemaBuilder smallInteger($length = null)
 * @method ColumnSchemaBuilder integer($length = null)
 * @method ColumnSchemaBuilder bigInteger($length = null)
 * @method ColumnSchemaBuilder float($precision = null)
 * @method ColumnSchemaBuilder double($precision = null)
 * @method ColumnSchemaBuilder decimal($precision = null, $scale = null)
 * @method ColumnSchemaBuilder dateTime($precision = null)
 * @method ColumnSchemaBuilder timestamp($precision = null)
 * @method ColumnSchemaBuilder time($precision = null)
 * @method ColumnSchemaBuilder date()
 * @method ColumnSchemaBuilder binary($length = null)
 * @method ColumnSchemaBuilder boolean()
 * @method ColumnSchemaBuilder money($precision = null, $scale = null)
 */
class Migration extends \yii\db\Migration
{
    /**
     * @var array список таблиц, по которым нужно обновить схему после подтверждения/отката транзакции
     */
    protected $tablesForSchemaRefresh = [];

    /**
     * @inheritdoc
     */
    public function createTable($table, $columns, $options = null)
    {
        parent::createTable($table, $columns, $options);
        foreach ($columns as $column => $type) {
            if ($type instanceof ColumnSchemaBuilder) {
                if ($type->isIndexed || $type->isUnique) {
                    $this->createIndexWithAutoName($table, $column, $type->isUnique);
                }
                if ($type->foreignKeyData) {
                    $this->addForeignKeyWithAutoName($table, $column, $type->foreignKeyData[0], $type->foreignKeyData[1]);
                }
            }
        }
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function renameTable($table, $newName)
    {
        parent::renameTable($table, $newName);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function dropTable($table)
    {
        parent::dropTable($table);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function addColumn($table, $column, $type)
    {
        parent::addColumn($table, $column, $type);
        if ($type instanceof ColumnSchemaBuilder) {
            if ($type->isIndexed || $type->isUnique) {
                $this->createIndexWithAutoName($table, $column, $type->isUnique);
            }
            if ($type->foreignKeyData) {
                $this->addForeignKeyWithAutoName($table, $column, $type->foreignKeyData[0], $type->foreignKeyData[1]);
            }
        }
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function dropColumn($table, $column)
    {
        parent::dropColumn($table, $column);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function renameColumn($table, $name, $newName)
    {
        parent::renameColumn($table, $name, $newName);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function alterColumn($table, $column, $type)
    {
        parent::alterColumn($table, $column, $type);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        parent::addPrimaryKey($name, $table, $columns);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function dropPrimaryKey($name, $table)
    {
        parent::dropPrimaryKey($name, $table);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function dropForeignKey($name, $table)
    {
        parent::dropForeignKey($name, $table);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function createIndex($name, $table, $columns, $unique = false)
    {
        parent::createIndex($name, $table, $columns, $unique);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function dropIndex($name, $table)
    {
        parent::dropIndex($name, $table);
        $this->refreshTableSchema($table);
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        $parentResult = parent::up();
        if ($parentResult !== false) {
            foreach ($this->tablesForSchemaRefresh as $table) {
                $time = microtime(true);
                $this->db->getTableSchema($table, true);
                echo '    > refresh schema for table ' . $table . ' (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
            }
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $parentResult = parent::down();
        if ($parentResult !== false) {
            foreach ($this->tablesForSchemaRefresh as $table) {
                $time = microtime(true);
                $this->db->getTableSchema($table, true);
                echo '    > refresh schema for table ' . $table . ' (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
            }
        }
        return $parentResult;
    }

    /**
     * Сброс кэша схемы таблицы
     * @param string $table название таблицы
     */
    protected function refreshTableSchema($table)
    {
        $this->tablesForSchemaRefresh[$table] = $table;
    }

    /**
     * Сброс значения последовательности таблицы
     * @param string $table название таблицы
     * @param mixed $value новое значение последовательности
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    protected function resetSequence($table, $value = null)
    {
        $this->db->createCommand()->resetSequence($table, $value)->execute();
    }

    /**
     * Добавление индекса (с автогенериацией его имени)
     * @param string       $table
     * @param string|array $columns
     * @param boolean      $unique
     */
    public function createIndexWithAutoName($table, $columns, $unique = false)
    {
        $name = md5(($unique ? 'u' : 'i') . 'x_' . $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns));
        $this->createIndex($name, $table, $columns, $unique);
    }

    /**
     * Удаление индекса (с автосгенерированным именем)
     * @param string       $table
     * @param string|array $columns
     * @param boolean      $unique
     */
    public function dropIndexWithAutoName($table, $columns, $unique = false)
    {
        $name = md5(($unique ? 'u' : 'i') . 'x_' . $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns));
        $this->dropIndex($name, $table);
    }

    /**
     * Добавление внешнего ключа (с автогенериацией его имени)
     * @param string       $table
     * @param string|array $columns
     * @param string       $refTable
     * @param string|array $refColumns
     * @param string       $delete
     * @param string       $update
     */
    public function addForeignKeyWithAutoName($table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        $name = md5('fk_' . $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns));
        $this->addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    /**
     * Удаление внешнего ключа (с автосгенерированным именем)
     * @param string       $table
     * @param string|array $columns
     */
    public function dropForeignKeyWithAutoName($table, $columns)
    {
        $name = md5('fk_' . $table . '_' . (is_array($columns) ? implode('_', $columns) : $columns));
        $this->dropForeignKey($name, $table);
    }

    /**
     * Создание таблицы для модели перечисления
     * @param string $table
     * @param array $values
     * @throws \yii\db\Exception
     * @throws \yii\base\NotSupportedException
     */
    protected function createEnumTable($table, $values)
    {
        $this->createTable($table, [
            'id'   => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
        ]);
        $rows = [];
        foreach ($values as $id => $name) {
            $rows[] = ['id' => $id, 'name' => $name];
        }
        $this->batchInsert($table, ['id', 'name'], $rows);
        $this->resetSequence($table);
    }

    /**
     * Создание таблицы для модели справочника
     * @param string $table
     * @param array $specificColumns
     */
    protected function createReferenceTable($table, $specificColumns = [])
    {
        $columnsBefore = [
            'id'        => $this->primaryKey(),
            'name'      => $this->string(256)->notNull()->indexed(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ];
        $columnsAfter = [
            'create_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'update_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'create_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'update_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ];
        $this->createTable($table, array_merge($columnsBefore, $specificColumns, $columnsAfter));
    }

    /**
     * Создание кросс-таблицы
     * @param string $table
     * @param array $specificColumns
     */
    protected function createCrossTable($table, $specificColumns = [])
    {
        $columnsBefore = [
            'id' => $this->primaryKey(),
        ];
        $columnsAfter = [
            'create_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'create_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ];
        $this->createTable($table, array_merge($columnsBefore, $specificColumns, $columnsAfter));
    }

    /**
     * Создание таблицы для модели документа
     * @param string $table
     * @param array $specificColumns
     */
    protected function createDocumentTable($table, $specificColumns = [])
    {
        $columnsBefore = [
            'id'              => $this->primaryKey(),
            'date'            => $this->timestamp()->notNull()->indexed(),
            'status_id'       => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_document_status}}', 'id'),
            'organization_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_organization}}', 'id'),
            'document_basis_type_id' => $this->integer()->foreignKey('{{%sys_entity}}', 'id'),
            'document_basis_id' => $this->integer(),
        ];
        $columnsAfter = [
            'create_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'update_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'create_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'update_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ];
        $this->createTable($table, array_merge($columnsBefore, $specificColumns, $columnsAfter));
    }

    /**
     * Создание таблицы для табличной части
     * @param string $table
     * @param string $parentTable
     * @param array $specificColumns
     */
    protected function createTablePartTable($table, $parentTable, $specificColumns = [])
    {
        $columnsBefore = [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->notNull()->indexed()->foreignKey($parentTable, 'id'),
        ];
        $columnsAfter = [
            'create_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'create_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ];
        $this->createTable($table, array_merge($columnsBefore, $specificColumns, $columnsAfter));
    }

    /**
     * Создание регистра
     * @param string $table
     * @param array $specificColumns
     */
    protected function createRegisterTable($table, $specificColumns = [])
    {
        $columnsBefore = [
            'id' => $this->primaryKey(),
            'date' => $this->timestamp()->notNull(),
        ];
        $columnsAfter = [
            'create_user_id' => $this->integer()->foreignKey('{{%ref_user}}', 'id'),
            'create_date'    => $this->timestamp()->notNull()->defaultExpression('NOW()'),
        ];
        $this->createTable($table, array_merge($columnsBefore, $specificColumns, $columnsAfter));
    }

    /**
     * Создание регистра накопления
     * @param string $table
     * @param array $specificColumns
     */
    protected function createRegisterTableWithDocumentBasis($table, $specificColumns = [])
    {
        $columnsBefore = [
            'document_basis_type_id' => $this->integer()->notNull()->foreignKey('{{%sys_entity}}', 'id'),
            'document_basis_id' => $this->integer()->notNull(),
        ];
        $this->createRegisterTable($table, array_merge($columnsBefore, $specificColumns));
        $this->createIndexWithAutoName($table, ['document_basis_type_id', 'document_basis_id']);
    }

    /**
     * Создание системной таблицы
     * @param string $table
     * @param array $specificColumns
     */
    protected function createSystemTable($table, $specificColumns = [])
    {
        $columnsBefore = [
            'id' => $this->primaryKey(),
        ];
        $this->createTable($table, array_merge($columnsBefore, $specificColumns));
    }
}
