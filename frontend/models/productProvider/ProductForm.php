<?php

namespace frontend\models\productProvider;

use common\models\form\SystemForm;
use common\models\reference\Product;
use common\models\reference\ProductCategory;
use common\models\reference\Unit;
use common\models\reference\User;
use Yii;
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
 * @property string $price цена
 * @property string $unit_id единица измерения
 * @property string $product_category_id категория продукта
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
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function getDataProvider()
    {
        if (User::isProductProvider()) {
            $productProvider = Yii::$app->user->identity->getProfile();

            $query = Product::find()->andWhere(['product_provider_id' => $productProvider->id]);

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
        return new ActiveDataProvider([]);
    }

    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [
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
            [
                'attribute' => 'quantity',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Product $rowModel */
                    if (isset($rowModel->quantity)) {
                        return Html::encode($rowModel->quantity);
                    }
                    return '';
                }
            ],
        ];
    }

    /**
     * @return mixed|void
     * @throws UserException
     * @throws InvalidConfigException
     */
    public function proceed()
    {
        if (User::isProductProvider()) {
            $productProvider = Yii::$app->user->identity->getProfile();
            $product = Product::find()
                ->andWhere([
                    'name' => $this->name,
                    'product_provider_id' => $productProvider->id
                ])
                ->one();
            if (!$product) {
                $product = new Product();
                $product->name = $this->name;
                $product->is_active = true;
                $product->price = $this->price;
                $product->unit_id = $this->unit_id;
                $product->product_category_id = $this->product_category_id;
                $product->product_provider_id = $productProvider->id;
                $product->save();
                Yii::$app->session->setFlash('success', 'Продукт успешно добавлен.');
            } else {
                Yii::$app->session->setFlash('error', 'Такой продукт уже существует');
            }
        }
    }
}