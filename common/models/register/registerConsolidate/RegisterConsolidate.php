<?php

namespace common\models\register\registerConsolidate;

use common\components\DateTime;
use common\models\register\Register;
use common\queries\ActiveQuery;

/**
 * Базовая модель регистра сведений
 */
abstract class RegisterConsolidate extends Register
{
    /**
     * Сохранение экземпляра модели (перед сохранением проверяется наличие
     * уже существующей записи по измерениям регистра)
     * @param boolean $runValidation необходимость выполнения валидации перед сохранением
     * @param array $attributes массив атрибутов для сохранения
     * @param boolean $onlyIfChanged массив атрибутов для сохранения
     * @return boolean результат операции
     * @throws \yii\base\UserException
     */
    public function save($runValidation = true, $attributes = null, $onlyIfChanged = false)
    {
        if ($this->isNewRecord) {
            $conditions = [];
            foreach($this->getDimensions() as $div) {
                if (array_key_exists($div, $this->attributes)) {
                    $conditions[$div] = $this->$div;
                }
            }
            if ($conditions) {
                $exist = static::findOne($conditions);
                if ($exist) {
                    $primaryKey = $exist->primaryKey();
                    foreach ($primaryKey as $pkAttribute) {
                        $this->{$pkAttribute} = $exist->{$pkAttribute};
                    }
                    $this->setIsNewRecord(false);
                    $this->setOldAttributes($exist->attributes);
                }
            }
        }
        return parent::save();
    }

    /**
     * Получение среза последних значений в регистре на заданную дату
     *
     * @param null|string|DateTime $date
     * @param string $tableAlias1
     * @param string $tableAlias2
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    static public function findSlice($date = null, $tableAlias1 = 't1', $tableAlias2 = 't2')
    {
        $joinCondition = $condition = ['AND'];
        $params = [];
        if ($date) {
            $joinCondition[] = $tableAlias2 . '.date <= :date';
            $condition[] = $tableAlias1 . '.date <= :date';
            $params[':date'] = $date;
        }

        foreach (static::getSpecialDimensions() as $dimension) {
            $joinCondition[] = $tableAlias1 . '.' . $dimension . ' = ' . $tableAlias2 . '.' . $dimension;
        }

        $joinCondition[] = $tableAlias2 . '.date > ' . $tableAlias1 . '.date';

        $condition[] = $tableAlias2 . '.date IS NULL';

        $query = static::find()
            ->alias($tableAlias1)
            ->leftJoin(static::tableName() . ' AS ' . $tableAlias2, $joinCondition)
            ->andWhere($condition, $params);
        return $query;
    }
}
