<?php

use backend\controllers\BackendModelController;
use backend\widgets\GridView\GridViewToolbar;
use backend\widgets\JsTree\JsTree;
use common\models\reference\ProductCategory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var ProductCategory $model */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var ProductCategory $filterModel */

$this->title = $model->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var BackendModelController $controller */
$controller = $this->context;
$treeWidgetId = 'product-category';
$pjaxTreeWidgetId = 'pjax-' . $treeWidgetId;

?>
<div class="category-index">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php

            // Вывод блока дерева категорий
            $toolbarLayout = [['treeRefresh'], ['add', 'edit'], ['search']];

            echo GridViewToolbar::widget([
                'options' => [],
                'layout' => $toolbarLayout,
                'tokens' => [
                    'treeRefresh' => function () use ($pjaxTreeWidgetId) {
                        Yii::$app->view->registerJs("
                            $('#treeRefresh').click(function(e){
                                e.preventDefault();
                                $('#treeEditMode').data('turnOn', false);
                                $.pjax.reload('#" . $pjaxTreeWidgetId . "', {
                                    replace: true,
                                    timeout: 5000,
                                });
                            });
                        ");
                        return Html::a('<i class="glyphicon glyphicon-refresh"></i>', '#', [
                            'id' => 'treeRefresh',
                            'class' => 'btn btn-primary',
                            'title' => 'Обновить дерево категорий',
                        ]);
                    },
                    'add' => function () use ($treeWidgetId) {
                        return Html::a('<i class="glyphicon glyphicon-plus"></i> Добавить рубрику', ['create'], [
                            'id' => 'categoryAdd',
                            'class' => 'btn btn-success',
                            'title' => 'Добавить рубрику',
                        ]);
                    },
                    'edit' => function () use ($treeWidgetId) {
                        $templateToken = 'xxx';
                        $categoryUpdateUrlTemplate = Url::to(['/reference/product-category/update', 'id' => $templateToken]);
                        Yii::$app->view->registerJs("
                            $('#categoryEdit').click(function(e){
                                e.preventDefault();
                                var selectedCategoryId = $('#" . $treeWidgetId . "').data('categoryId');
                                if (selectedCategoryId) {
                                    window.location.href = '{$categoryUpdateUrlTemplate}'.split('{$templateToken}').join(selectedCategoryId);
                                }
                            });
                        ");
                        return Html::a('<i class="glyphicon glyphicon-edit"></i> Редактировать рубрику', '#', [
                            'id' => 'categoryEdit',
                            'class' => 'btn btn-success',
                            'title' => 'Редактировать рубрику',
                        ]);
                    },
                    'search' => function () use ($treeWidgetId) {
                        return Html::tag('div',
                            Html::tag('div',
                                Html::textInput('search_tree', '', ['id' => 'search_tree', 'class' => 'form-control']) .
                                Html::tag('span', Html::button('Поиск', ['class' => 'btn btn-default']), ['id' => 'search_tree_btn', 'class' => 'input-group-btn']),
                                ['class' => 'input-group']),
                            ['class' => 'form-inline']);
                    },
                ],
            ]);

            echo '</div>';
            $pjaxTreeWidget = Pjax::begin(['id' => $pjaxTreeWidgetId]);

            $treeWidget = JsTree::begin([
                'id' => $treeWidgetId,
                'jsOptions' => [
                    'core' => [
                        'check_callback' => new JsExpression("
                            function(operation, node, node_parent, node_position, more) {
                                if ((operation == 'move_node') && (node['id'] == 'root')) {
                                    return false;
                                }
                                return true;
                            }
                        "),
                        'multiple' => false,
                    ],
                    'checkbox' => [
                        'visible' => false,
                        'three_state' => false,
                    ],
                    'dnd' => [
                        'is_draggable' => false,
                        'copy' => false,
                    ],
                    'search' => [
                        'show_only_matches' => true,
                        'search_callback' => new JsExpression("
                            function(keyword, node) {
                                if (node['id'] == keyword) {
                                    return true;
                                }
                                var f = new $.vakata.search(keyword, true, {caseSensitive : false});
                                return f.search(node['text']).isMatch;
                            }
                        "),
                    ],
                    'plugins' => ['wholerow', 'checkbox', 'dnd', 'state', 'search'],
                ],
            ]);
            echo $treeWidget->getHtmlTree(ProductCategory::getTree(), function ($node) {

                return [
                    'id' => $node['id'],
                    'text' => $node['name'],
                    'icon' => ($node['data']['is_active'] ? 'glyphicon glyphicon-folder-close' : 'glyphicon glyphicon-trash'),
                ];
            });
            $treeWidget->end();

            $pjaxTreeWidget->end();

            $this->registerJs("
                $('#" . $pjaxTreeWidgetId . "').on('pjax:complete', function() {
                    registerTreeEventHandlers();
                });
                var timeout = false;
                var searchCallback = function() {
                    if (timeout) { 
                        clearTimeout(timeout);
                    }
                    timeout = setTimeout(function() {
                        $('#" . $treeWidgetId . "').jstree(true).search($('#search_tree').val());
                    }, 250);
                };
                $('#search_tree_btn').click(searchCallback);
                $('#search_tree').change(searchCallback);
                function registerTreeEventHandlers() {
                    $('#" . $treeWidgetId . "')
                        .on('changed.jstree', function(e, data) {
                            if (data['action'] == 'select_node') {
                                console.log(data['node']);
                                $(this).find('li[role=treeitem] .jstree-wholerow').css('background', '');
                                $(this).find('li[role=treeitem][id=' + data['node']['id'] + '] .jstree-wholerow').first().css('background', '#FAE49B');
                                var nodeId = data['node']['id'] != 'root' ? data['node']['id'] : '';
                                $('#" . $treeWidgetId . "').data('categoryId', nodeId);
                            }
                        })
                        .on('move_node.jstree', function(e, data) {
                            if (data['node']['ignore_move_callback'] || data['node']['id'] == 'root') {
                                data['node']['ignore_move_callback'] = false;
                                return;
                            }
                            var tree = $(this);
                            var categoryId = data['node']['id'];
                            var oldParentId = data['old_parent'];
                            var newParentId = data['parent'];
                            if (newParentId == 'root') {
                                newParentId = '#';
                            }
                            $.ajax({
                                url: 'move',
                                method: 'POST',
                                dataType: 'json',
                                data: {id: categoryId, newParentId: newParentId},
                                error: function(){
                                    alert('Ошибка перемещения категории \"' + data['node']['text'] + '\"');
                                    data['node']['ignore_move_callback'] = true;
                                    tree.jstree('move_node', data['node'], data['old_parent']);
                                },
                            });
                        });
                }
                registerTreeEventHandlers();    
            ");

            ?>
        </div>
    </div>
