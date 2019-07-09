<?php

namespace backend\widgets\GridView;

use yii\base\Widget;
use yii\widgets\Pjax;

/**
 * Виджет для вывода списка с панелью инструментов
 */
class GridViewWithToolbar extends Widget
{
    /**
     * @var array настройки виджета панели инструментов (false отключает панель инструментов)
     */
    public $gridToolbarOptions;

    /**
     * @var array|boolean настройки виджета PJAX (false отключает PJAX)
     */
    public $gridPjaxOptions;

    /**
     * @var array настройки виджета списка
     */
    public $gridOptions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->gridOptions['id'])) {
            $this->gridOptions['id'] = $this->id . '-grid';
        }
        if ($this->gridToolbarOptions !== false) {
            $this->gridToolbarOptions['gridId'] = $this->gridOptions['id'];
        }
        if ($this->gridPjaxOptions !== false) {
            if (!isset($this->gridPjaxOptions['id'])) {
                $this->gridPjaxOptions['id'] = $this->gridOptions['id'] . '-pjax';
            }
            if ($this->gridToolbarOptions !== false) {
                $this->gridToolbarOptions['gridPjaxId'] = $this->gridPjaxOptions['id'];
            }
        }
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function run()
    {
        if ($this->gridToolbarOptions !== false) {
            echo GridViewToolbar::widget($this->gridToolbarOptions);
        }
        if ($this->gridPjaxOptions !== false) {
            $pjaxGridWidget = Pjax::begin($this->gridPjaxOptions);
            if (isset($this->gridOptions['columns']) && is_callable($this->gridOptions['columns'])) {
                $this->gridOptions['columns'] = call_user_func($this->gridOptions['columns']);
            }
            echo GridView::widget($this->gridOptions);
            $pjaxGridWidget->end();
        } else {
            echo GridView::widget($this->gridOptions);
        }
    }
}
