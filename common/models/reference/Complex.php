<?php

namespace common\models\reference;

use backend\controllers\reference\ReferenceController;
use backend\widgets\ActiveForm;
use common\models\enum\ComplexType;
use common\models\enum\FoodType;
use common\models\tablepart\ComplexMeal;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Модель справочника "Комплекс"
 *
 * Свойства:
 * @property integer $complex_type_id
 * @property integer $food_type_id
 * @property float $price
 * @property string $description
 *
 * Отношения:
 * @property ComplexType $complexType
 * @property FoodType $foodType
 * @property ComplexMeal[] $complexMeals
 */
class Complex extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Комплекс';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Комплексы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['complex_type_id', 'food_type_id'], 'integer'],
            [['price'], 'number', 'min' => 0],
            [['description'], 'string'],
            [['complex_type_id', 'food_type_id'], 'required'],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'complex_type_id' => 'Период питания',
            'food_type_id' => 'Тип комплекса',
            'price' => 'Цена',
            'description' => 'Описание',
            'complexMeals' => 'Блюда (состав комплекса)',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplexType()
    {
        return $this->hasOne(ComplexType::class, ['id' => 'complex_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFoodType()
    {
        return $this->hasOne(FoodType::class, ['id' => 'food_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplexMeals()
    {
        return $this->hasMany(ComplexMeal::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'complexMeals' => ComplexMeal::className(),
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     * @param $tablePartRelation
     * @param $form
     * @param bool $readonly
     * @return array
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getTablePartColumns($tablePartRelation, $form, $readonly = false)
    {
        /** @var ActiveForm $form */
        $model = $this;
        $parentResult = ReferenceController::getTablePartColumns($model, $tablePartRelation, $form, $readonly);
        if ($tablePartRelation == 'complexMeals') {
            // Колонка продукты
            $parentResult['price'] = [
                'format' => 'raw',
                'label' => 'Цена',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var ComplexMeal $rowModel */
                    $result = '';
                    if (!$rowModel->isNewRecord && isset($rowModel->meal->price)) {
                        $result = Html::encode($rowModel->meal->price);
                    }
                    return $result;
                }
            ];
            $parentResult['sum'] = [
                'format' => 'raw',
                'label' => 'Сумма',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var ComplexMeal $rowModel */
                    $result = 0;
                    if (!$rowModel->isNewRecord && isset($rowModel->meal->price)) {
                        $result = $rowModel->meal->price * $rowModel->meal_quantity;
                    }
                    return number_format($result, 2);
                }
            ];
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
            foreach ($this->complexMeals as $complexMeal) {
                if (isset($complexMeal->meal)) {
                    $sum += $complexMeal->meal_quantity * $complexMeal->meal->price;
                }
            }
            $this->price = $sum;
        }
        return $parentResult;
    }
}