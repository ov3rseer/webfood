<?php

namespace common\models\document;

use backend\controllers\document\DocumentController;
use common\models\enum\DocumentStatus;
use common\models\reference\ProductProvider;
use common\models\register\registerAccumulate\ProviderWarehouse;
use common\models\tablepart\ArrivalProduct;
use ReflectionException;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Модель документа "Поступление продуктов"
 *
 * Свойства:
 * @property integer $product_provider_id
 *
 * Отношения:
 * @property ProductProvider $productProvider
 * @property ArrivalProduct[] $arrivalProducts
 */
class Arrival extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Поступление продуктов';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Поступления продуктов';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['product_provider_id'], 'integer'],
            [['product_provider_id'], 'required'],
            [['status_id'], 'validateStatus']
        ]);
    }

    /**
     * Запрет на проведение документа без продуктов
     */
    public function validateStatus()
    {
        if ($this->status_id == DocumentStatus::POSTED) {
            if (!$this->arrivalProducts) {
                $this->addError('summary', 'Необходимо добавить хотя-бы один продукт.');
            }
            foreach ($this->arrivalProducts as $arrivalProduct) {
                if ($arrivalProduct->quantity == 0) {
                    $this->addError('summary', 'Количестов продуктов должно быть больше 0.');
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'product_provider_id' => 'Поставщик продуктов',
            'arrivalProducts' => 'Продукты',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductProvider()
    {
        return $this->hasOne(ProductProvider::class, ['id' => 'product_provider_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArrivalProducts()
    {
        return $this->hasMany(ArrivalProduct::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getSettingsForDependentRegisters()
    {
        return [
            // Склады постащиков
            ProviderWarehouse::class => [
                'balance_error' => function () {
                    return '';
                },
                'function' => function (Arrival $documentModel) {
                    $result = [];
                    foreach ($documentModel->arrivalProducts as $row){
                        $result[] = [
                            'product_provider_id' => $documentModel->product_provider_id,
                            'product_id' => $row->product_id,
                            'quantity' => $row->quantity,
                        ];
                    }
                    return $result;
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        $result = parent::getTableParts();
        if ($this->product_provider_id) {
            $result = array_merge([
                'arrivalProducts' => ArrivalProduct::class,
            ], $result);
        }
        return $result;
    }

    /**
     * @param $tablePartRelation
     * @param $form
     * @param bool $readonly
     * @return array
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function getTablePartColumns($tablePartRelation, $form, $readonly = false)
    {
        $model = $this;
        $parentResult = DocumentController::getTablePartColumns($model, $tablePartRelation, $form, $readonly);
        if ($tablePartRelation == 'arrivalProducts') {
            $parentResult['unit'] = [
                'format' => 'raw',
                'label' => 'Ед. измерения',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var ArrivalProduct $rowModel */
                    $result = '';
                    if ($rowModel->product && $rowModel->product->unit) {
                        $result = Html::encode($rowModel->product->unit);
                    }
                    return $result;
                }
            ];
        }
        return $parentResult;
    }
}