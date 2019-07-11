<?php


namespace common\models\reference;


class Unit extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Единица измерения';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Единицы измерения';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name_full'], 'string'],
            [['code', 'international_abbreviation'], 'string', 'max' => 3],
            [['international_abbreviation'], 'filter', 'filter' => 'strtoupper'],
            [['name_full', 'code', 'international_abbreviation'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Краткое наименование',
            'name_full' => 'Полное наименование',
            'code' => 'Код',
            'international_abbreviation' => 'Международное сокращение',
        ]);
    }
}