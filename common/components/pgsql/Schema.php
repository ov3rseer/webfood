<?php

namespace common\components\pgsql;

/**
 * Расширенный класс схемы БД
 */
class Schema extends \yii\db\pgsql\Schema
{
    const TYPE_DATETIME = 'timestamp (0) with time zone';
    const TYPE_TIMESTAMP = 'timestamp (0) with time zone';

    /**
     * @inheritdoc
     * @return ColumnSchemaBuilder column schema builder instance
     */
    public function createColumnSchemaBuilder($type, $length = null)
    {
        return new ColumnSchemaBuilder($type, $length);
    }
}
