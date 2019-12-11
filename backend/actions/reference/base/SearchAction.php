<?php

namespace backend\actions\reference\base;

use common\queries\ActiveQuery;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

/**
 * Действие для быстрого поиска моделей по ключевой фразе
 */
class SearchAction extends \backend\actions\base\SearchAction
{
    /**
     * Построение запроса
     * @param string $term
     * @param string $conditions
     * @return ActiveQuery $this
     * @throws InvalidConfigException
     * @throws NotSupportedException
     */
    protected function buildQuery($term = '', $conditions = '')
    {
        $query = parent::buildQuery($term, $conditions);
        $query->andWhere(['is_active' => true]);
        return $query;
    }
}
