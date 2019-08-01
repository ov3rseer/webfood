<?php

namespace frontend\models\request;

use frontend\models\FrontendForm;

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