<?php


namespace common\models\reference;

/**
 * Модель справочник "Контрагент"
 */
class Contractor extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Контрагент';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Контрагенты';
    }
}