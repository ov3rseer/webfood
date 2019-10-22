<?php

namespace frontend\models\serviceObject;

use common\models\form\SystemForm;
use common\models\reference\Product;
use common\models\reference\ProductCategory;
use common\models\reference\Unit;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Форма добавления продуктов
 */
class ProductForm extends SystemForm
{
    /**
     * @var string название продукта
     */
    public $name;

    /**
     * @var string цена
     */
    public $price;

    /**
     * @var string единица измерения
     */
    public $unit_id;

    /**
     * @var string категория продукта
     */
    public $product_category_id;

    /**
     * @return string|void
     */
    public function getName()
    {
        return 'Продукты';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'price', 'unit_id', 'product_category_id'], 'required'],
            [['unit_id', 'product_category_id'], 'integer'],
            [['price'], 'number', 'min' => 0],
            [['name'], 'string', 'max' => 255],
            [['name'], 'filter', 'filter' => 'trim'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Наименование продукта',
            'price' => 'Цена',
            'unit_id' => 'Единица измерения',
            'product_category_id' => 'Категория продукта',
        ]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getUnit()
    {
        return Unit::find()->andWhere(['id' => $this->unit_id]);
    }


    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getProductCategory()
    {
        return ProductCategory::find()->andWhere(['id' => $this->product_category_id]);
    }


    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Product $rowModel */
                    return Html::encode($rowModel);
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Product $rowModel */
                    if ($rowModel->is_active == true) {
                        return 'Да';
                    } else {
                        return 'Нет';
                    }
                },
            ],
            [
                'attribute' => 'price',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Product $rowModel */
                    return Html::encode($rowModel->price);
                },
            ],
            [
                'attribute' => 'unit_id',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Product $rowModel */
                    return Html::encode($rowModel->unit);
                },
            ],
            [
                'attribute' => 'product_category_id',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Product $rowModel */
                    return Html::encode($rowModel->productCategory);
                },
            ],

        ];
        return $columns;
    }

    /**
     * @return mixed|void
     */
    public function proceed()
    {

    }
}