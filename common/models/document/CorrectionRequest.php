<?php

namespace common\models\document;

/**
 * Модель документа "Корректировка заявки"
 */
class CorrectionRequest extends Request
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Корректировка заявки';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Корректировки заявок';
    }
}
