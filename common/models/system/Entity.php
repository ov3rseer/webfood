<?php

namespace common\models\system;

/**
 * Класс системной таблицы сущностей
 *
 * @property string $class_name имя класса сущности
 */
class Entity extends System
{
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'class_name' => 'Имя класса',
        ]);
    }

    /**
     * Получение ID по имени класса
     * @param string $className имя класса
     * @return integer|null
     */
    static public function getIdByClassName($className)
    {
        $result = self::findOne(['class_name' => $className]);
        return empty($result) ? null : $result->id;
    }

    /**
     * Получение имени класса по ID
     * @param integer $id идентификатор сущности
     * @return string|null
     */
    static public function getClassNameById($id)
    {
        $result = self::findOne(['id' => $id]);
        return empty($result) ? null : $result->class_name;
    }
}
