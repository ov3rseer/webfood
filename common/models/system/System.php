<?php

namespace common\models\system;

use common\models\ActiveRecord;

/**
 * Базовая модель записи системной таблицы
 */
abstract class System extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    protected static $tablePrefix = 'sys_';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => 'ID',
        ]);
    }
}
