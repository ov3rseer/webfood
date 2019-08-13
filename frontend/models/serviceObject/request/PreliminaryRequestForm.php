<?php

namespace frontend\models\serviceObject\request;

/**
 * Форма "Предварительная заявка"
 */
class PreliminaryRequestForm extends RequestForm
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Предварительная заявка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Предварительные заявки';
    }
}