<?php

namespace frontend\models\serviceObject;

use common\models\enum\ComplexType;
use common\models\enum\FoodType;
use common\models\form\SystemForm;
use common\models\reference\Complex;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Форма добавления комплексов
 */
class ComplexForm extends SystemForm
{
    /**
     * @var string название блюда
     */
    public $name;

    /**
     * @var string категория блюда
     */
    public $complex_type_id;

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
    public $meals;

    /**
     * @return string|void
     */
    public function getName()
    {
        return 'Комплексы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'description', 'complex_type_id', 'food_type_id'], 'required'],
            [['complex_type_id', 'food_type_id'], 'integer'],
            [['description'], 'string'],
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
            'complex_type_id' => 'Период питания',
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
    public function getComplexType()
    {
        return ComplexType::find()->andWhere(['id' => $this->complex_type_id]);
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
                    /** @var Complex $rowModel */
                    return Html::encode($rowModel);
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Complex $rowModel */
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
                    /** @var Complex $rowModel */
                    return Html::encode($rowModel->foodType);
                },
            ],
            [
                'attribute' => 'complex_type_id',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Complex $rowModel */
                    return Html::encode($rowModel->complexType);
                },
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Complex $rowModel */
                    return Html::encode($rowModel->description);
                },
            ],
            [
                'attribute' => 'meals',
                'label' => 'Блюда (состав)',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var Complex $rowModel */
                    $result = '';
                    if (!empty($rowModel->complexMeals)) {
                        $result .= '<table class="table table-striped table-bordered">';
                        $result .= '<tr>';
                        $result .= '<td>';
                        $result .= '<strong>Наименование блюда</strong>';
                        $result .= '</td>';
                        $result .= '<td>';
                        $result .= '<strong>Количество</strong>';
                        $result .= '</td>';
                        $result .= '<td>';
                        $result .= '<strong>Цена</strong>';
                        $result .= '</td>';
                        $result .= '</tr>';
                        foreach ($rowModel->complexMeals as $complexMeal) {
                            $result .= '<tr>';
                            $result .= '<td>';
                            $result .= Html::encode($complexMeal->meal);
                            $result .= '</td>';
                            $result .= '<td>';
                            $result .= Html::encode($complexMeal->meal_quantity);
                            $result .= '</td>';
                            $result .= '<td>';
                            $result .= Html::encode($complexMeal->meal->price) . ' руб.';
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
     * @return mixed|void
     */
    public function proceed()
    {

    }
}