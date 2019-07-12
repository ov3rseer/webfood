<?php

namespace backend\widgets\JsTree;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class JsTree extends Widget
{
    /**
     * @var array массив настроек JS-виджета
     */
    public $jsOptions = [];

    /**
     * @var array массив настроек контейнера
     */
    public $options;

    /**
     * @var bool
     */
    public $encode = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!empty($this->options['id'])) {
            $this->id = $this->options['id'];
        } else {
            $this->options['id'] = $this->id;
        }
        JsTreeAsset::register($this->getView());
        echo Html::beginTag('div', $this->options);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag('div');
        $this->getView()->registerJs("$('#" . $this->id . "').jstree(" . Json::encode($this->jsOptions) .");");
    }

    /**
     * Подготовка данных дерева
     * @param array $tree
     * @param callable $callback
     * @return array
     */
    public function normalizeTree($tree, $callback)
    {
        $result = [];
        foreach ($tree as $node) {
            $result[] = $this->_normalizeNode($node, $callback);
        }
        return $result;
    }

    /**
     * Подготовка данных ветки дерева
     * @param $node
     * @param $callback
     * @return mixed
     */
    protected function _normalizeNode($node, $callback)
    {
        $result = call_user_func($callback, $node);
        $result['children'] = [];
        if (isset($node['children'])) {
            foreach ($node['children'] as $childNode) {
                $result['children'][] = $this->_normalizeNode($childNode, $callback);
            }
        }
        if (!isset($result['li_attr']) || !is_array($result['li_attr'])) {
            $result['li_attr'] = [];
        }
        if (!isset($result['a_attr']) || !is_array($result['a_attr'])) {
            $result['a_attr'] = [];
        }
        return $result;
    }

    /**
     * Вывод дерева для HTML-отображения
     * @param array $tree
     * @param callable $callback
     * @return string
     */
    public function getHtmlTree($tree, $callback)
    {
        $normalizedTree = $this->normalizeTree($tree, $callback);
        $result = Html::beginTag('ul');
        foreach ($normalizedTree as $normalizedNode) {
            $result .= $this->_getHtmlNode($normalizedNode);
        }
        $result .= Html::endTag('ul');
        return $result;
    }

    /**
     * Вывод ветки дерева для HTML-отображения
     * @param array $node
     * @return string
     */
    protected function _getHtmlNode($node)
    {
        $liSpecialOptions = [];
        if (isset($node['icon'])) {
            $liSpecialOptions['icon'] = $node['icon'];
        }
        foreach (['opened', 'disabled', 'selected'] as $state) {
            if (isset($node['state'][$state])) {
                $liSpecialOptions[$state] = (boolean)$node['state'][$state];
            }
        }
        if ($liSpecialOptions) {
            $node['li_attr']['data-jstree'] = Json::encode($liSpecialOptions);
        }
        $node['li_attr']['id'] = $node['id'];
        $result = Html::beginTag('li', $node['li_attr']);
        $result .= Html::beginTag('a', $node['a_attr']);
        $result .= $this->encode ? Html::encode($node['text']) : $node['text'];
        $result .= Html::endTag('a');
        if ($node['children']) {
            $result .= Html::beginTag('ul');
            foreach ($node['children'] as $childNode) {
                $result .= $this->_getHtmlNode($childNode);
            }
            $result .= Html::endTag('ul');
        }
        $result .= Html::endTag('li');
        return $result;
    }
}
