<?php

namespace frontend\models\serviceObject\request;

use common\components\DateTime;
use common\models\cross\RequestDateProduct;
use common\models\document\Request;
use common\models\enum\DocumentStatus;
use common\models\tablepart\ContractProduct;
use common\models\tablepart\RequestDate;
use Exception;
use common\models\form\Form;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Форма для создания заявок на поставку товаров
 */
class RequestForm extends Form
{
    /**
     * Сценарий для предварительной заявки
     */
    const SCENARIO_PRELIMINARY = 'preliminary-request';

    /**
     * Сценарий для корректировки заявок
     */
    const SCENARIO_CORRECTION = 'correction-request';

    public $contract_id;
    public $service_object_id;
    public $service_object;
    public $productQuantities;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        $result[self::SCENARIO_PRELIMINARY] = $result[self::SCENARIO_DEFAULT];
        $result[self::SCENARIO_CORRECTION] = $result[self::SCENARIO_DEFAULT];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object', 'contract_id', 'service_object_id', 'productQuantities'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object' => 'Заказчик (Код заказчика)',
            'contract_id' => 'Код договора: место поставки',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        switch ($this->scenario) {
            case RequestForm::SCENARIO_PRELIMINARY:
                return 'Предварительная заявка';
            case RequestForm::SCENARIO_CORRECTION:
                return 'Корректировка заявки';
        }
        return 'Заявка';
    }

    public function getDataProvider()
    {
        return [];
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getRequestTable($week)
    {
        $result = [];
        $requestDatesIdMap = [];
        $requestDateProducts = null;

        /** @var ContractProduct[] $contractProducts */
        $contractProducts = ContractProduct::find()->andWhere(['parent_id' => $this->contract_id])->all();

        if (isset($week['beginWeek']) && isset($week['endWeek'])) {
            /** @var Request $request */
            $request = Request::find()
                ->alias('r')
                ->innerJoin(RequestDate::tableName() . ' AS rd', 'r.id = rd.parent_id')
                ->andWhere(['r.service_object_id' => $this->service_object_id, 'r.contract_id' => $this->contract_id, 'r.status_id' => DocumentStatus::DRAFT])
                ->andWhere(['between', 'rd.week_day_date', $week['beginWeek'], $week['endWeek']])
                ->with('requestDates')
                ->one();
            if ($request) {
                foreach ($request->requestDates as $requestDate) {
                    $requestDatesIdMap[$requestDate->id] = $requestDate->week_day_date->format('d-m-Y');
                }
                if (!empty($requestDatesIdMap)) {
                    /** @var RequestDateProduct[] $requestDateProducts */
                    $requestDateProducts = RequestDateProduct::find()->andWhere(['request_date_id' => array_keys($requestDatesIdMap)])->all();
                }
            }

            $hasContractProducts = false;
            $hasRequestDateProducts = false;
            if ($this->scenario == self::SCENARIO_PRELIMINARY) {
                if ($contractProducts) {
                    $hasContractProducts = true;
                }
                if ($requestDateProducts) {
                    $hasRequestDateProducts = true;
                }
            } elseif ($this->scenario == self::SCENARIO_CORRECTION) {
                if ($contractProducts && $requestDateProducts) {
                    $hasContractProducts = true;
                    $hasRequestDateProducts = true;
                } elseif (!$this->contract_id) {
                    Yii::$app->session->setFlash('info', 'Если после нажатия кнопки "Сформировать" таблица не формируется, перейдите в раздел '
                        . Html::a('Предварительная заявка', ['index', 'contractTypeId' => 1, 'action' => 'preliminary-request'], ['style' => 'color:red;'])
                        . ', чтобы сформировать заявку.');
                }
            }

            if ($hasContractProducts) {
                foreach ($contractProducts as $contractProduct) {
                    $result[$contractProduct->product_id] = [
                        'product_code' => $contractProduct->product->product_code,
                        'product_name' => Html::encode($contractProduct->product),
                        'product_unit' => Html::encode($contractProduct->product->unit),
                        'quantities' => []
                    ];
                }
            }
            if ($hasRequestDateProducts) {
                foreach ($requestDateProducts as $requestDateProduct) {
                    $result[$requestDateProduct->product_id]['quantities'][$requestDatesIdMap[$requestDateProduct->request_date_id]] = [
                        'planned_quantity' => $requestDateProduct->planned_quantity,
                        'current_quantity' => $requestDateProduct->current_quantity,
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * Формирование колонок для таблицы
     * @param $week
     * @return array
     * @throws Exception
     */
    public function getColumns($week)
    {
        $columns = [];
        if (isset($week['beginWeek']) && isset($week['endWeek'])) {
            $columns = [
                'product_code' => [
                    'header' => 'Код товара',
                    'headerOptions' => ['style' => 'width:28px;'],
                    'format' => 'raw',
                    'value' => function ($rowModel) {
                        /** @var ContractProduct $rowModel */
                        return $rowModel['product_code'] ?: '';
                    },
                ],
                'product_name' => [
                    'header' => 'Наименование товара',
                    'headerOptions' => ['style' => 'width:28px;'],
                    'format' => 'raw',
                    'value' => function ($rowModel) {
                        /** @var ContractProduct $rowModel */
                        return $rowModel['product_name'] ?: '';
                    },
                ],
                'product_unit' => [
                    'header' => 'Ед. изм.',
                    'headerOptions' => ['style' => 'width:28px;'],
                    'format' => 'raw',
                    'value' => function ($rowModel) {
                        /** @var ContractProduct $rowModel */
                        return $rowModel['product_unit'] ?: '';
                    },
                ]
            ];

            $weekDayMap = [
                'Monday' => 'Понедельник',
                'Tuesday' => 'Вторник',
                'Wednesday' => 'Среда',
                'Thursday' => 'Четверг',
                'Friday' => 'Пятница',
            ];

            $weekDayDateMap = [];
            $beginWeek = new DateTime($week['beginWeek']);
            $endWeek = new DateTime($week['endWeek']);
            while ($beginWeek < $endWeek) {
                $weekDayDateMap[$beginWeek->format('l')] = $beginWeek->format('d-m-Y');
                $beginWeek->modify('+ 1 days');
            }

            foreach ($weekDayDateMap as $weekDayId => $weekDay) {
                $header = '';
                $header .= Html::beginTag('table', $options = ['class' => 'table table-striped table-bordered text-center', 'style' => 'margin: 0;']);
                $header .= Html::beginTag('tbody');

                $header .= Html::beginTag('tr');
                $header .= Html::tag('td', Html::encode('Дата поставки'), $options = ['colspan' => 2]);
                $header .= Html::endTag('tr');

                $header .= Html::beginTag('tr');
                $header .= Html::tag('td', Html::encode($weekDay), $options = ['colspan' => 2]);
                $header .= Html::endTag('tr');

                $header .= Html::beginTag('tr');
                $header .= Html::tag('td', Html::encode($weekDayMap[$weekDayId]), $options = ['colspan' => 2]);
                $header .= Html::endTag('tr');

                $header .= Html::beginTag('tr');
                $header .= Html::tag('td', Html::encode('Планируемое количество'));
                if ($this->scenario == self::SCENARIO_CORRECTION) {
                    $header .= Html::tag('td', Html::encode('Фактическое количество'));
                }
                $header .= Html::endTag('tr');

                $header .= Html::endTag('tbody');
                $header .= Html::endTag('table');

                $columns[$weekDayId] = [
                    'header' => $header,
                    'headerOptions' => ['style' => 'width: 28px; padding: 0;'],
                    'format' => 'raw',
                    'value' => function ($rowModel, $productId) use ($weekDay) {
                        /** @var ContractProduct|RequestDateProduct $rowModel */
                        $options = ['class' => 'form-control', 'style' => 'border-radius: 0;'];
                        if ($this->scenario == self::SCENARIO_CORRECTION) {
                            $options = array_merge($options, ['readonly' => true]);
                        }
                        $plannedQuantity = Html::textInput('RequestForm[productQuantities][' . $productId . '][' . $weekDay . '][planned_quantity]',
                            isset($rowModel['quantities'][$weekDay]) ? $rowModel['quantities'][$weekDay]['planned_quantity'] : 0,
                            $options);

                        $currentQuantity = '';
                        if ($this->scenario == self::SCENARIO_CORRECTION) {
                            $currentQuantity = Html::textInput('RequestForm[productQuantities][' . $productId . '][' . $weekDay . '][current_quantity]',
                                isset($rowModel['quantities'][$weekDay]) ? $rowModel['quantities'][$weekDay]['current_quantity'] : 0,
                                ['class' => 'form-control', 'style' => 'border-radius: 0;']);
                        }

                        $result = '<table style="margin: 0; width: 100%;">
                            <tbody>
                                <tr>
                                    <td>'
                            . $plannedQuantity . '
                                    </td>
                                    <td>'
                            . $currentQuantity . '
                                    </td>
                                </tr>
                            </tbody>
                        </table>';

                        return $result;

                    },
                ];
            }
//        $columns = array_merge($columns, [
//            'planned_bulk_by_contract' => [
//                'header' => 'Планируемый объём по договору',
//                'headerOptions' => ['style' => 'width:28px;'],
//                'format' => 'raw',
//                'value' => function ($rowModel) {
//                    /** @var ContractProduct $rowModel */
//                    return '';
//                },
//            ],
//            'shipped' => [
//                'header' => 'Отгружено',
//                'headerOptions' => ['style' => 'width:28px;'],
//                'format' => 'raw',
//                'value' => function ($rowModel) {
//                    /** @var ContractProduct $rowModel */
//                    return '';
//                },
//            ],
//            'available_for_order' => [
//                'header' => 'Доступно для заказа',
//                'headerOptions' => ['style' => 'width:28px;'],
//                'format' => 'raw',
//                'value' => function ($rowModel) {
//                    /** @var ContractProduct $rowModel */
//                    return '';
//
//                },
//            ],
//        ]);
        }
        return $columns;
    }
}