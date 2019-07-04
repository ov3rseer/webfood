<?php

namespace common\queries;

/**
 * Класс для работы с запросами, связанными с моделями Reference
 */
class ReferenceQuery extends ActiveQuery
{
    /**
     * Фильтр только по статусу элементов справочника
     * @param boolean $isActive
     * @return static
     */
    public function active($isActive = true)
    {
        return $this->andWhere([$this->getAlias() . '.is_active' => $isActive]);
    }
}
