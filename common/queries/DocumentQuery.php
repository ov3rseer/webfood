<?php

namespace common\queries;

use common\models\enum\DocumentStatus;
use common\models\document\Document;

/**
 * Класс для работы с запросами, связанными с моделями Document
 */
class DocumentQuery extends ActiveQuery
{
    /**
     * Фильтр только по документам с указанным статусом
     * @param int $statusId
     * @return static
     */
    public function byStatus($statusId)
    {
        /** @var Document $modelClass */
        $modelClass = $this->modelClass;
        return $this->andWhere([$modelClass::tableName() . '.status_id' => $statusId]);
    }

    /**
     * Фильтр только по проведенным документам
     * @return static
     */
    public function posted()
    {
        return $this->byStatus(DocumentStatus::POSTED);
    }
}
