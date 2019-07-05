<?php

namespace common\models\enum;

/**
 * Статус документа
 */
class DocumentStatus extends Enum
{
    /**
     * Черновик
     */
    const DRAFT = 1;

    /**
     * Проведен
     */
    const POSTED = 2;

    /**
     * Помечен на удаление
     */
    const DELETED = 3;
}
