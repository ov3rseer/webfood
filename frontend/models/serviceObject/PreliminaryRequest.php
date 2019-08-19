<?php

namespace frontend\models\serviceObject;

/**
 * Форма "Предварительная заявка"
 */
class PreliminaryRequest extends Request
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