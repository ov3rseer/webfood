<?php

namespace backend\widgets\GridView;

use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\GridView as YiiGridView;
use yii\helpers\ArrayHelper;

/**
 * Виджет для вывода списка моделей в табличном виде
 */
class GridView extends YiiGridView
{
    /**
     * @var string шаблон отображения
     */
    public $layout = "{items}\n{pager}\n{summary}";

    /**
     * @var array атрибуты контейнера
     */
    public $options = [
        'style' => 'overflow:auto;',
    ];

    /**
     * @var array настройки для столбца с чекбоксами. Установить false, если необходимо скрыть столбец
     */
    public $checkboxColumn = [
        'headerOptions' => ['style' => 'width:28px;']
    ];

    /**
     * @var array настройки для столбца с действиями. Установить false, если необходимо скрыть столбец
     */
    public $actionColumn = [
        'template' => '{update}',
    ];

    /**
     * @inheritdoc
     */
    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        if ($this->columns) {
            $hasActionColumn = false;
            $hasCheckboxColumn = false;
            foreach ($this->columns as $i => $column) {
                if (is_array($column)) {
                    if (isset($column['class']) && is_a($column['class'], ActionColumn::class, true)) {
                        $hasActionColumn = true;
                    }
                    if (isset($column['class']) && is_a($column['class'], CheckboxColumn::class, true)) {
                        $hasCheckboxColumn = true;
                    }
                }
            }
            if ($this->actionColumn !== false && !$hasActionColumn) {
                array_unshift($this->columns, ArrayHelper::merge(['class' => ActionColumn::class], $this->actionColumn));
            }
            if ($this->checkboxColumn !== false && !$hasCheckboxColumn) {
                array_unshift($this->columns, ArrayHelper::merge(['class' => CheckboxColumn::class], $this->checkboxColumn));
            }
            parent::initColumns();
        }
    }
}
