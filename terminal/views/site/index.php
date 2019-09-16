<?php

/* @var  yii\web\View $this */
/* @var  Form $model */
/* @var  integer $categoryId */

use common\models\enum\MealType;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use common\models\form\Form;
use yii\helpers\Html;


$this->title = 'Terminal WebFood';

if (isset($categoryId)) {
    $category = MealCategory::findOne(['id' => $categoryId]);
    echo Html::tag('h1', $category);

    /** @var Meal[] $meals */
    $meals = Meal::find()
        ->andWhere([
            'is_active' => true,
            'meal_category_id' => $categoryId,
            'meal_type_id' => MealType::BUFFET_MEALS
        ])
        ->orderBy('id ASC')
        ->all();
    echo Html::beginTag('div', ['class' => 'row']);
    foreach ($meals as $meal) {
        echo Html::beginTag('div', ['class' => 'col-xs-12 col-md-3']);
        echo Html::beginTag('div', ['class' => 'card', 'style' => 'width: 18rem;']);
        echo Html::beginTag('div', ['class' => 'card-body']);
        echo Html::tag('h5', Html::encode($meal), ['class' => 'card-title']);
        echo Html::tag('p', Html::encode($meal->description), ['class' => 'card-text']);
        echo Html::tag('p', Html::encode($meal->price). ' &#8381;', ['class' => 'card-text price']);
        echo Html::a('Добавить', ['meal-form/index', 'mealId' => $meal->id, 'categoryId' => $categoryId], ['class' => 'btn btn-success']);
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('div');
    }
    echo Html::endTag('div');
}
