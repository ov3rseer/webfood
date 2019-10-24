<?php

namespace frontend\models\serviceObject;

use common\models\form\SystemForm;
use common\models\reference\Product;
use common\models\reference\ProductCategory;
use common\models\reference\Unit;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\ActiveDataProvider;
use yii\data\BaseDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Форма добавления продуктов
 *
 * Свойства:
 * @property string $name наименование
 * @property BaseDataProvider $dataProvider источник данных отчета
 * @property array $columns колонки отчета
 */
class ProductForm extends SystemForm
{
    /**
     * @var string название продукта
     */
    public $name;

    /**
     * @var string код продукта
     */
    public $product_code;

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
            [['product_code'], 'string', 'max' => 9],
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
            'product_code' => 'Код продукта',
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
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function getDataProvider()
    {
        $query = Product::find();

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 20,
                'pageSizeLimit' => false,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
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
     * @throws UserException
     */
    public function proceed()
    {
        $product = new Product();
        $product->name = $this->name;
        $product->product_code = $this->product_code;
        $product->is_active = true;
        $product->price = $this->price;
        $product->unit_id = $this->unit_id;
        $product->product_category_id = $this->product_category_id;
        $product->save();
    }
}