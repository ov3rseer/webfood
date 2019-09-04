<?php

namespace common\models\reference;

use backend\widgets\ActiveField;

/**
 * Модель справочника "Единицы измерения"
 *
 * @property string $name_full
 * @property string $code
 * @property string $international_abbreviation
 * @property string $auth_key
 */
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
     * Магическая функция приведения объекта к строке
     * @return string
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : ($this->name_full ?? $this->name);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['code', 'international_abbreviation'], 'string', 'max' => 3],
            [['name_full'], 'string', 'max' => 1024],
            [['name_full'], 'filter', 'filter' => 'trim'],
            [['international_abbreviation'], 'filter', 'filter' => 'strtoupper'],
            [['code', 'international_abbreviation'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'      => 'Краткое наименование',
            'name_full' => 'Полное наименование',
            'code'      => 'Код',
            'international_abbreviation' => 'Международное сокращение',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['name_full']['displayType'] = ActiveField::STRING;
        }
        return $this->_fieldsOptions;
    }
}