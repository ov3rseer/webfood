<?php

namespace common\models\reference;

use backend\controllers\reference\ReferenceController;
use backend\widgets\ActiveForm;
use common\models\enum\MealType;
use common\models\tablepart\MealProduct;
use ReflectionException;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Модель справочника "Блюдо"
 *
 * Свойства
 * @property integer $meal_category_id
 * @property float   $price
 * @property string  $description
 *
 * Отношения:
 * @property MealProduct[] $mealProducts
 * @property MealCategory $mealCategory
 */
class Meal extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Блюдо';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Блюда';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['meal_category_id', 'meal_type_id'], 'integer'],
            [['name', 'meal_category_id', 'meal_type_id'], 'required'],
            [['description'], 'string'],
            [['price'], 'number', 'min' => 0],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'mealProducts'      => 'Продукты (состав блюда)',
            'meal_category_id'  => 'Категория блюда',
            'meal_type_id'      => 'Тип блюда',
            'price'             => 'Цена',
            'description'       => 'Описание',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMealCategory()
    {
        return $this->hasOne(MealCategory::className(), ['id' => 'meal_category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMealType()
    {
        return $this->hasOne(MealType::className(), ['id' => 'meal_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMealProducts()
    {
        return $this->hasMany(MealProduct::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'mealProducts' => MealProduct::className(),
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     * @param $tablePartRelation
     * @param $form
     * @param bool $readonly
     * @return array
     * @throws InvalidConfigException
     * @throws ReflectionException
     */
    public function getTablePartColumns($tablePartRelation, $form, $readonly = false)
    {
        /** @var ActiveForm $form */
        $model = $this;
        $parentResult = ReferenceController::getTablePartColumns($model, $tablePartRelation, $form, $readonly);
        if ($tablePartRelation == 'mealProducts') {
            $parentResult['price-per-unit'] = [
                'format' => 'raw',
                'label' => 'Цена за ед. измерения',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var MealProduct $rowModel */
                    $result = '';
                    if (!$rowModel->isNewRecord && $rowModel->product) {
                        $result = Html::encode($rowModel->product->price) . ' руб. за ' . Html::encode($rowModel->product->unit);
                    }
                    return $result;
                }
            ];
            $parentResult['sum'] = [
                'format' => 'raw',
                'label' => 'Сумма',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var MealProduct $rowModel */
                    $result = 0;
                    if (!$rowModel->isNewRecord && isset($rowModel->product->price)) {
                        $result = $rowModel->product->price * $rowModel->product_quantity;
                    }
                    return number_format($result, 2);
                }
            ];
            $parentResult['product_quantity']['label'] = 'Вес (количество)';
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            $sum = 0;
            foreach ($this->mealProducts as $mealProduct) {
                if (isset($mealProduct->product)) {
                    if ($mealProduct->product->unit_id == $mealProduct->unit_id) {
                        $sum += $mealProduct->product_quantity * $mealProduct->product->price;
                    }
                }
            }
            $this->price = $sum;
        }
        return $parentResult;
    }
}