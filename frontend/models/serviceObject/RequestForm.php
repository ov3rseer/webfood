<?php

namespace frontend\models\serviceObject;

use backend\widgets\ActiveField;
use common\models\document\Request;
use common\models\reference\ServiceObject;
use Exception;
use common\models\form\Form;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Форма для создания заявок на поставку товаров
 */
class RequestForm extends Form
{
    public $service_object_id;
    public $delivery_day;
    public $product_provider_id;

    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Заявка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Заявки';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_id'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object_id' => 'Заказчик',
        ]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getServiceObject()
    {
        return ServiceObject::find()->andWhere(['id' => $this->service_object_id]);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['delivery_day']['displayType'] = ActiveField::DATE;
        }
        return $this->_fieldsOptions;
    }

    /**
     * Формирование колонок для таблицы
     * @return array
     * @throws Exception
     */
    public function getColumns()
    {
        $columns = [
            'request' => [
                'header' => 'Заявка',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Request $rowModel */
                    return Html::encode($rowModel);
                },
            ],
            'requestStatus',
            'delivery_day',
            'productProvider',
            'requestProducts' => [
                'label' => 'Продукты',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Request $rowModel */
                    $result = '';
                    foreach ($rowModel->requestProducts as $requestProduct) {
                        $result .= Html::encode($requestProduct->product) . ' - ' . Html::encode($requestProduct->quantity) . ' ' . Html::encode($requestProduct->product->unit). '<br>';
                    }
                    return $result;
                }
            ]
        ];


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

        return $columns;
    }
}