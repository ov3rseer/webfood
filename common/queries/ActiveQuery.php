<?php

namespace common\queries;

use yii\db\ActiveQuery as YiiActiveQuery;
use yii\db\ActiveRecord;

class ActiveQuery extends YiiActiveQuery
{
    /**
     * @var string
     */
    protected $_alias;

    /**
     * @inheritdoc
     */
    public function alias($alias)
    {
        $this->_alias = $alias;
        return parent::alias($alias);
    }

    /**
     * Получение алиаса основной таблицы запроса
     * @return string
     */
    public function getAlias()
    {
        if ($this->_alias) {
            $result = $this->_alias;
        } else {
            /** @var ActiveRecord $modelClass */
            $modelClass = $this->modelClass;
            $tableName = $modelClass::tableName();
            if (preg_match('/^(.*?)\s+({{\w+}}|\w+)$/', $tableName, $matches)) {
                $result = $matches[2];
            } else {
                $result = $tableName;
            }
        }
        return $result;
    }
}
