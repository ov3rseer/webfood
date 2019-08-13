<?php

use backend\widgets\GridView\GridView;
use common\models\cross\RequestDateProduct;
use common\models\tablepart\ContractProduct;
use yii\helpers\Html;

/* @var \frontend\models\request\RequestTableForm $model */
/* @var yii\data\ArrayDataProvider $dataProvider */
/* @var $weekDayDateMap */

/** @var \frontend\controllers\request\RequestTableController $controller */
$controller = $this->context;

$reflection = new ReflectionClass($model->className());
$shortClassName = $reflection->getShortName();
$gridWidgetId = 'grid-' . $shortClassName;

$weekDayMap = [
    'Monday'    => 'Понедельник',
    'Tuesday'   => 'Вторник',
    'Wednesday' => 'Среда',
    'Thursday'  => 'Четверг',
    'Friday'    => 'Пятница',
];

$columns = [
    'product_code' => [
        'header' => 'Код товара',
        'headerOptions' => ['style' => 'width:28px;'],
        'format' => 'raw',
        'value' => function($rowModel) {
            /** @var ContractProduct $rowModel */
            return $rowModel['product_code'] ?: '';
        },
    ],
    'product_name' => [
        'header' => 'Наименование товара',
        'headerOptions' => ['style' => 'width:28px;'],
        'format' => 'raw',
        'value' => function($rowModel) {
            /** @var ContractProduct $rowModel */
            return $rowModel['product_name'] ?: '';
        },
    ],
    'product_unit' => [
        'header' => 'Ед. изм.',
        'headerOptions' => ['style' => 'width:28px;'],
        'format' => 'raw',
        'value' => function($rowModel) {
            /** @var ContractProduct $rowModel */
            return $rowModel['product_unit'] ?: '';
        },
    ]
];

if (!empty($dataProvider->allModels)) {

    $i = 0;

    foreach ($weekDayMap as $weekDayId => $weekDay) {
        $weekDayDate = $weekDayDateMap[$i];

        $header  = '';
        $header .= Html::beginTag('table', $options = ['class' => 'table table-striped table-bordered text-center', 'style' => 'margin: 0;']);
        $header .= Html::beginTag('tbody');

        $header .= Html::beginTag('tr');
        $header .= Html::tag('td', Html::encode('Дата поставки'), $options = ['colspan' => 2]);
        $header .= Html::endTag('tr');

        $header .= Html::beginTag('tr');
        $header .= Html::tag('td', Html::encode($weekDayDate), $options = ['colspan' => 2]);
        $header .= Html::endTag('tr');

        $header .= Html::beginTag('tr');
        $header .= Html::tag('td', Html::encode($weekDay), $options = ['colspan' => 2]);
        $header .= Html::endTag('tr');

        $header .= Html::beginTag('tr');
        $header .= Html::tag('td', Html::encode('Планируемое количество'));
        $header .= Html::tag('td', Html::encode('Фактическое количество'));
        $header .= Html::endTag('tr');

        $header .= Html::endTag('tbody');
        $header .= Html::endTag('table');

        $columns[$weekDayId] = [
            'header' => $header,
            'headerOptions' => ['style' => 'width: 28px; padding: 0;'],
            'format' => 'raw',
            'value' => function($rowModel) use ($weekDayDate) {
                /** @var ContractProduct|RequestDateProduct $rowModel */
                return
                    '<table style="margin: 0;">
                        <tbody>
                            <tr>
                                <td>
                                    '.Html::input(
                                  'text',
                                        Html::encode(($rowModel['product_code'] ?: '').'_'.$weekDayDate.'_planned_quantity'),
                                  isset($rowModel['quantities'][$weekDayDate]) ? $rowModel['quantities'][$weekDayDate]['planned_quantity'] : 0,
                                        ['class' => 'form-control', 'style' => 'border-radius: 0;']).'
                                </td>
                                <td>
                                    '.Html::input('text', Html::encode(($rowModel['product_code'] ?: '').'_'.$weekDayDate.'_current_quantity'), isset($rowModel['quantities'][$weekDayDate]) ? $rowModel['quantities'][$weekDayDate]['current_quantity'] : 0, ['class' => 'form-control', 'style' => 'border-radius: 0;']).'
                                </td>
                            </tr>
                        </tbody>
                    </table>';
            },
        ];

        $i++;
    }

}

$columns = array_merge($columns, [
    'planned_bulk_by_contract' => [
        'header' => 'Планируемый объём по договору',
        'headerOptions' => ['style' => 'width:28px;'],
        'format' => 'raw',
        'value' => function($rowModel) {
            /** @var ContractProduct $rowModel */
            return '';
        },
    ],
    'shipped' => [
        'header' => 'Отгружено',
        'headerOptions' => ['style' => 'width:28px;'],
        'format' => 'raw',
        'value' => function($rowModel) {
            /** @var ContractProduct $rowModel */
            return '';
        },
    ],
    'available_for_order' => [
        'header' => 'Доступно для заказа',
        'headerOptions' => ['style' => 'width:28px;'],
        'format' => 'raw',
        'value' => function($rowModel) {
            /** @var ContractProduct $rowModel */
            return '';
        },
    ],
]);

?>
<div class="reference-index">

    <?php
    if (!empty($dataProvider->allModels)) {
        //$form = ActiveForm::begin(['id' => 'form-request', 'method' => 'get']);

        echo GridView::widget([
            'id' => $gridWidgetId,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'checkboxColumn' => false,
            'actionColumn' => false,
        ]);
        ?>

        <input name="action" value="request-table" hidden>

        <div class="form-group">
            <?= Html::button('Сохранить', ['class' => 'btn btn-primary', 'name' => 'request-button', 'onclick' => 'saveRequest("'.$gridWidgetId.'")']) ?>
        </div>

        <?php
        //ActiveForm::end();
    }
    ?>

</div>