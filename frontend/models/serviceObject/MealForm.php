<?php

namespace frontend\models\serviceObject;

use common\models\enum\FoodType;
use common\models\form\SystemForm;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use common\models\reference\Product;
use common\models\reference\Unit;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\BaseDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Форма добавления блюд
 *
 * Свойства:
 * @property string $name наименование
 * @property BaseDataProvider $dataProvider источник данных
 * @property array $columns колонки
 */
class MealForm extends SystemForm
{
    /**
     * @var string название блюда
     */
    public $name;

    /**
     * @var string категория блюда
     */
    public $meal_category_id;

    /**
     * @var string категория блюда
     */
    public $food_type_id;

    /**
     * @var string категория блюда
     */
    public $description;

    /**
     * @var array продукты
     */
    public $products;

    /**
     * @return string|void
     */
    public function getName()
    {
        return 'Блюда';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'price', 'meal_category_id', 'food_type_id'], 'required'],
            [['meal_category_id', 'food_type_id'], 'integer'],
            [['description'], 'string'],
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
            'name' => 'Наименование блюда',
            'description' => 'Описание',
            'food_type_id' => 'Тип блюда',
            'meal_category_id' => 'Категория блюда',
        ]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFoodType()
    {
        return FoodType::find()->andWhere(['id' => $this->food_type_id]);
    }


    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getMealCategory()
    {
        return MealCategory::find()->andWhere(['id' => $this->meal_category_id]);
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
                    /** @var Meal $rowModel */
                    return Html::encode($rowModel);
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Meal $rowModel */
                    if ($rowModel->is_active == true) {
                        return 'Да';
                    } else {
                        return 'Нет';
                    }
                },
            ],
            [
                'attribute' => 'food_type_id',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Meal $rowModel */
                    return Html::encode($rowModel->foodType);
                },
            ],
            [
                'attribute' => 'meal_category_id',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Meal $rowModel */
                    return Html::encode($rowModel->mealCategory);
                },
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Meal $rowModel */
                    return Html::encode($rowModel->description);
                },
            ],
            [
                'attribute' => 'products',
                'label' => 'Продукты (состав)',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Meal $rowModel */
                    $result = '';
                    if (!empty($rowModel->mealProducts)) {
                        $result .= '<table class="table table-striped table-bordered">';
                        $result .= '<tr>';
                        $result .= '<td>';
                        $result .= '<strong>Наименование продукта</strong>';
                        $result .= '</td>';
                        $result .= '<td>';
                        $result .= '<strong>Количество</strong>';
                        $result .= '</td>';
                        $result .= '<td>';
                        $result .= '<strong>Цена</strong>';
                        $result .= '</td>';
                        $result .= '</tr>';
                        foreach ($rowModel->mealProducts as $mealProduct) {
                            $result .= '<tr>';
                            $result .= '<td>';
                            $result .= Html::encode($mealProduct->product);
                            $result .= '</td>';
                            $result .= '<td>';
                            $result .= Html::encode($mealProduct->product_quantity) . ' ' . Html::encode($mealProduct->unit->name);
                            $result .= '</td>';
                            $result .= '<td>';
                            $result .= Html::encode($mealProduct->product->price) . ' руб. за ' . Html::encode($mealProduct->product->unit);
                            $result .= '</td>';
                            $result .= '</tr>';
                        }
                        $result .= '</table>';
                    }
                    return $result;
                },
            ],
        ];
        return $columns;
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getProducts()
    {
        return Product::find()->select('name')->indexBy('id')->column();
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getUnits()
    {
        return Unit::find()->select('name_full')->indexBy('id')->column();
    }

    /**
     * @throws UserException
     */
    public function proceed()
    {
        $meal = new Meal();
        $meal->is_active = true;
        $meal->name = $this->name;
        $meal->meal_category_id = $this->meal_category_id;
        $meal->food_type_id = $this->food_type_id;
        $meal->description = $this->description;
        $meal->save();
    }

}