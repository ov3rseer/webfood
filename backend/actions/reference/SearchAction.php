<?php

namespace backend\actions\reference\base;

use common\queries\ActiveQuery;

/**
 * Действие для быстрого поиска моделей по ключевой фразе
 */
class SearchAction extends \backend\actions\base\SearchAction
{
    /**
     * Построение запроса
     * @param string $term
     * @return ActiveQuery $this
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     */
    protected function buildQuery($term = '')
    {
        $query = parent::buildQuery($term);
        $query->andWhere(['is_active' => true]);
        return $query;
    }
}
