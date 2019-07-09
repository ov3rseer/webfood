<?php

namespace common\components\mysql;

/**
 * Расширенный класс схемы БД
 */
class Schema extends \yii\db\mysql\Schema
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
