<?php

namespace frontend\models\serviceObject\request;

/**
 * Форма "Корректировка заявки"
 */
class CorrectionRequest extends Request
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Корректировка заявки';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Корректировка заявок';
    }
}