<?php

namespace backend\models\report;

use backend\models\form\Form;
use yii\data\BaseDataProvider;

/**
 * Базовый класс отчета
 *
 * Свойства:
 * @property string $name наименование отчета
 * @property BaseDataProvider $dataProvider источник данных отчета
 * @property array $columns колонки отчета
 */
abstract class Report extends Form
{
    /**
     * Получение наименования отчета
     * @return string
     */
    abstract public function getName();

    /**
     * Получение источника данных отчета
     * @return BaseDataProvider
     */
    abstract public function getDataProvider();

    /**
     * Получение колонок отчета
     * @return array
     */
    abstract public function getColumns();
}
