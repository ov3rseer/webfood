<?php

namespace backend\widgets\GridView;

use yii\base\Widget;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\PjaxAsset;

/**
 * Виджет для вывода панели инструментов
 */
class GridViewToolbar extends Widget
{
    /**
     * @var string ID списка
     */
    public $gridId;

    /**
     * @var string ID pjax-контейнера списка
     */
    public $gridPjaxId;

    /**
     * @var array атрибуты контейнера панели
     */
    public $options = [
        'class' => 'gridview-toolbar',
    ];

    /**
     * @var array шаблон панели (подмассив означает группу элементов)
     */
    public $layout = [
        ['refresh', 'create', 'delete'],
    ];

    /**
     * @var array расшифровка элементов панели
     */
    public $tokens = [];

    /**
     * @var string URL для создания нового элемента
     */
    public $createUrl;

    /**
     * @var string URL для удаления отмеченных элементов
     */
    public $deleteCheckedUrl;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->tokens['refresh'])) {
            $this->tokens['refresh'] = function() {
                $buttonId = $this->id . '-refresh';
                $this->getView()->registerJs("
                    $('#" . $buttonId . "').click(function(e){
                        e.preventDefault();
                        $.pjax.reload('#" . $this->gridPjaxId . "', {
                            replace: true,
                            timeout: 5000,
                        });
                    });
                ");
                return Html::a('<span class="glyphicon glyphicon-refresh"></span>', '#', [
                    'id' => $buttonId,
                    'class' => 'btn btn-primary',
                    'title' => 'Обновить список',
                ]);
            };
        }
        if (!isset($this->tokens['create'])) {
            $createUrl = $this->createUrl !== null ? $this->createUrl : Url::to(['create']);
            $this->tokens['create'] = function() use ($createUrl) {
                $buttonId = $this->id . '-create';
                return Html::a('<span class="glyphicon glyphicon-plus"></span>', $createUrl, [
                    'id' => $buttonId,
                    'class' => 'btn btn-success',
                    'title' => 'Создать новый элемент',
                ]);
            };
        }
        if (!isset($this->tokens['delete'])) {
            $deleteCheckedUrl = $this->deleteCheckedUrl !== null ? $this->deleteCheckedUrl : Url::to(['delete-checked']);
            $this->tokens['delete'] = function() use ($deleteCheckedUrl) {
                $buttonId = $this->id . '-delete';
                $this->getView()->registerJs("
                    $('#" . $buttonId . "').click(function(e){
                        e.preventDefault();
                        var checkedItems = $('#" . $this->gridId . " input[name^=selection]:checked');
                        if (!checkedItems.length) {
                            alert('Не выбраны элементы для удаления');
                            return;
                        }
                        if (!confirm('Вы действительно хотите удалить выделенные элементы?')) {
                            return;
                        }
                        var ids = [];
                        checkedItems.each(function(e, item){
                            ids.push($(item).val());
                        });
                        $.ajax({
                            url: '" . $deleteCheckedUrl . "',
                            data: {ids: ids},
                            dataType: 'json',
                            type: 'POST',
                            success: function(data) {
                                $.pjax.reload('#" . $this->gridPjaxId . "', {
                                    replace: true,
                                    timeout: 5000,
                                });
                            }
                        });
                    });
                ");
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                    'id' => $buttonId,
                    'class' => 'btn btn-danger',
                    'title' => 'Удалить отмеченные элементы',
                ]);
            };
        }
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function run()
    {
        PjaxAsset::register($this->view);
        $result = Html::beginTag('div', $this->options);
        $result .= Html::beginTag('div', ['class' => 'btn-toolbar']);
        foreach ($this->layout as $token) {
            if (is_array($token)) {
                $groupElements = [];
                foreach ($token as $subToken) {
                    if (isset($this->tokens[$subToken])) {
                        $groupElements[] = is_callable($this->tokens[$subToken]) ?
                            call_user_func($this->tokens[$subToken]) : $this->tokens[$subToken];
                    }
                }
                $result .= ButtonGroup::widget(['buttons' => $groupElements]);
            } else {
                if (isset($this->tokens[$token])) {
                    $result .= is_callable($this->tokens[$token]) ?
                        call_user_func($this->tokens[$token]) : $this->tokens[$token];
                }
            }
        }
        $result .= Html::endTag('div');
        $result .= Html::endTag('div');
        return $result;
    }
}
