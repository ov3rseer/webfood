<?php

namespace frontend\models\request;

/**
 * Форма "Корректировка заявки"
 */
class CorrectionRequestForm extends RequestForm
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