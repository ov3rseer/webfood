<?php


namespace common\models\document;


class CorrectionRequest extends Document
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