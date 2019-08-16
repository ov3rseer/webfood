<?php

namespace common\components\pgsql;

/**
 * Расширенный класс построителя схемы колонок
 *
 * @property boolean $isIndexed
 * @property boolean $isUnique
 * @property array   $foreignKeyData
 */
class ColumnSchemaBuilder extends \yii\db\ColumnSchemaBuilder
{
    /**
     * @var boolean нужно ли добавлять индекс для колонки
     */
    protected $isIndexed = false;

    /**
     * @var array данные внешнего ключа для колонки
     */
    protected $foreignKeyData = [];

    /**
     * Добавляет индекс для колонки
     * @return $this
     */
    public function indexed()
    {
        $this->isIndexed = true;
        return $this;
    }

     /**
     * Добавляет внешний ключ для колонки
     * @param $table
     * @param $column
     * @param $delete
     * @param $update
     * @return $this
     */
    public function foreignKey($table, $column, $delete = null, $update = null)
    {
        $this->foreignKeyData = [$table, $column, $delete, $update];
        return $this;
    }


    /**
     * Получение флага индекса
     * @return bool
     */
    public function getIsIndexed()
    {
        return $this->isIndexed;
    }

    /**
     * Получение флага уникальности
     * @return bool
     */
    public function getIsUnique()
    {
        return $this->isUnique;
    }

    /**
     * Получение данных внешнего ключа
     * @return array
     */
    public function getForeignKeyData()
    {
        return $this->foreignKeyData;
    }
}
