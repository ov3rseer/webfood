<?php

namespace common\models\enum;

use common\models\ActiveRecord;

/**
 * Модель элемента перечисления
 *
 * @property string $name наименование значения
 */
abstract class Enum extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'enum_';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'required']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Наименование',
        ]);
    }

    /**
     * Магическая функция приведения объекта к строке
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
