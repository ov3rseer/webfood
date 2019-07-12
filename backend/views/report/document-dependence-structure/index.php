<?php

use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use backend\widgets\GridView\GridViewToolbar;
use common\models\enum\DocumentStatus;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var yii\web\View $this */
/* @var backend\models\report\DocumentDependenceStructure $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="report-index">

<?php
$form = ActiveForm::begin([
    'method' => 'GET',
    'action' => Url::to(['']),
    'enableAjaxValidation' => false,
]);
?>
    <div class="report-attributes">
        <div class="container-fluid">
            <div class="display:none;">
            <?php
                foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
                    if ($fieldOptions['displayType'] == ActiveField::HIDDEN) {
                        echo $form->autoField($model, $field, $fieldOptions)->error(false)->label(false);
                    }
                }
            ?>
            </div>
            <div class="row">
            <?php
                foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
                    if ($fieldOptions['displayType'] != ActiveField::HIDDEN) {
                        echo '<div class="col-xs-12 col-sm-6 col-md-3">';
                        echo $form->autoField($model, $field, $fieldOptions);
                        echo '</div>';
                    }
                }
            ?>
            </div>
        </div>
    </div>
<?php
/** @noinspection PhpUnhandledExceptionInspection */
echo GridViewToolbar::widget([
    'layout' => ['refresh'],
    'tokens' => [
        'refresh' => function() {
            return Html::submitInput('Сформировать', ['class' => 'btn btn-primary']);
        }
    ]
]);

ActiveForm::end();

$treeWidgetId = 'document-dependence-structure';

$treeWidget = \backend\widgets\JsTree\JsTree::begin([
    'id' => $treeWidgetId,
    'encode' => false,
    'jsOptions' => [
        'plugins' => ['wholerow'],
        'core' => [
            'themes' => [
                'icons' => false,
            ],
        ],
    ],
    'options' => [
        'style' => 'border: 1px solid #ddd;',
    ],
]);
$colorsByStatus = [
    DocumentStatus::DELETED => '#da4f49',
    DocumentStatus::DRAFT   => '#000000',
    DocumentStatus::POSTED  => '#5bb75b',
];
echo $treeWidget->getHtmlTree($model->dataProvider->getModels(), function($node) use ($model, $colorsByStatus) {
    $name = Html::encode($node['name']);
    if (($model->document_basis_id == $node['data']['id']) && ($model->document_basis_type_id == $node['data']['type_id'])) {
        $name = '<strong>' . $name . '</strong>';
    }
    return [
        'id'   => $node['id'],
        'text' => '<span class="glyphicon glyphicon-file" style="color:' . $colorsByStatus[$node['data']['status_id']] . ';"></span>&nbsp;' . $name,
        'a_attr' => [
            'href' => $node['data']['url'],
        ],
    ];
});
$treeWidget->end();

$this->registerJs("
    $('#" . $treeWidgetId . "')
        .on('select_node.jstree', function(e, data) {
            var href = data.node.a_attr.ref;
            window.open(data.node.a_attr.href, data.node.a_attr.target);
        })
        .on('ready.jstree', function() { 
            $(this).jstree('open_all');
        });
");

?>

</div>
