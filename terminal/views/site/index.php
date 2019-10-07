<?php

/* @var  yii\web\View $this */
/* @var  Form $model */

/* @var  integer $categoryId */

use common\models\enum\FoodType;
use common\models\reference\Complex;
use common\models\reference\Meal;
use common\models\form\Form;
use common\models\reference\MealCategory;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/** @var Complex[] $complexes */
$complexes = Complex::find()
    ->andWhere([
        'is_active' => true,
        'food_type_id' => FoodType::BUFFET
    ])
    ->orderBy('id ASC')
    ->all();


foreach ($complexes as $complex) {
    $price = explode('.', $complex->price);
    $data[$complex->id] = [
        'name' => Html::encode($complex),
        'category' => 'Комплексы',
        'description' => $complex->description,
        'price' => $price[0] . '<small>,' . $price[1] . '</small> &#8381;/шт.',
        'parts' => [],
    ];
    foreach ($complex->complexMeals as $complexMeal){
        $data[$complex->id]['parts'][] =[(float)$complexMeal->meal_quantity, Html::encode($complexMeal->unit->name)];
    }
}



/** @var Meal[] $meals */
$meals = Meal::find()->andWhere([
    'is_active' => true,
    'food_type_id' => FoodType::BUFFET,
]);
if (isset($categoryId)) {
    $category = MealCategory::findOne(['id' => $categoryId]);
    if ($category) {
        $meals= $meals->andWhere(['meal_category_id' => $category->id]);
    }
}
$meals = $meals->orderBy('id ASC')->all();

$this->title = (isset($category) ? Html::encode($category) : 'ВебЕда');


if (!empty($meals)) {
    echo Html::beginTag('div', ['class' => 'row']);
    foreach ($meals as $meal) {
        $price = explode('.', $meal->price);

        echo Html::beginTag('div', ['class' => 'col-xs-4 px-4 py-3 h-3v']);
        echo Html::beginTag('div', ['class' => 'e_product e_product-card rounded shadow embed-responsive h-100', 'data' => ['e_product_id' => $meal->id]]);

        echo Html::beginTag('div', ['class' => 'embed-responsive-item icon-bg']);
        echo Html::tag('i', '', ['class' => 'icon fas fa-th-list mr-2']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'js_e_qty_toggle  card-body embed-responsive-item p-4', 'data' => ['for' => $meal->id]]);

        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('p', ['class' => 'subtitle mt-0 mb-2 col-xs-7']);
        echo Html::tag('span', Html::tag('i', '', ['class' => 'icon fas fa-th-list mr-2']) . Html::encode($meal->mealCategory), ['class' => 'ellipsis']);
        echo Html::endTag('p');
        echo Html::endTag('div');

        /* Price */
        echo Html::tag('h2', Html::encode($meal), ['class' => 'card-title mt-0 mb-2']);
        echo Html::beginTag('p', ['class' => 'card-text pb-1']);
        echo Html::tag('span', $price[0] . '<small>,' . $price[1] . '</small> &#8381;/шт.', ['class' => 'e_price-badge float-left']);
        echo Html::endTag('p');
        echo Html::endTag('div');
        /* END | Price */

        /* Button trigger modal */
        echo Html::button(Html::tag('i', '', ['class' => 'fas fa-info-circle mr-2']) . 'Подробнее',
            ['class' => 'card-addon pt-4 pr-4 pl-3 pb-3', 'data' => ['toggle' => 'modal', 'target' => '#myModal_' . $meal->id]]);
        /* END | Button trigger modal */

        /* Контролы количества */
        echo Html::beginTag('div', ['class' => 'card-footer text-center px-0 pt-3 m-0 pb-0']);
        echo Html::beginTag('div', ['class' => 'on-active row']);

        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo Html::button('', ['class' => 'js_e_qty_remove btn-qty qty-remove', 'data' => ['for' => $meal->id]]);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo Html::input('number', $meal->id, 0, ['class' => 'e_product-quantity-input hidden', 'min' => 0, 'step' => 1]);
        echo Html::tag('label', 0, ['class' => 'js_e_qty e_qty_addon m-0', 'data' => ['for' => $meal->id, 'unit' => ' шт.']]);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo Html::button('', ['class' => 'js_e_qty_add btn-qty qty-add', 'data' => ['for' => $meal->id]]);
        echo Html::endTag('div');

        echo Html::endTag('div');
        echo Html::endTag('div');
        /*  END | Контролы количества  */

        echo Html::endTag('div');
        echo Html::endTag('div');

        /*  Product modal  */
        Modal::begin(['options' => [
            'id' => 'myModal_' . $meal->id,
            'class' => 'e_modal modal fade',
            'tabindex' => '-1',
            'role' => 'dialog',
            'aria-labelledby' => 'myModalLabel',
            'data' => [
                'e_product_id' => $meal->id,
            ]],
            'bodyOptions' => ['class' => 'modal-body p-5'],
            'closeButton' => ['class' => 'close'],
            'header' => Html::beginTag('p', ['class' => 'subtitle mt-0 mb-2']) .
                Html::tag('i', '', ['class' => 'icon fas fa-th-list mr-2']) . Html::encode($meal->mealCategory)
                . Html::tag('h2', Html::encode($meal), ['class' => 'modal-title'])
                . Html::endTag('p'),
            'headerOptions' => ['class' => 'modal-header px-5 pt-5'],
        ]);

        echo Html::beginTag('div', ['class' => 'row price-and-qty mb-5']);
        echo Html::beginTag('div', ['class' => 'col-xs-6 e_qty-controls']);
        echo Html::beginTag('div', ['class' => 'input-group input-group-lg']);

        echo Html::beginTag('span', ['class' => 'input-group-btn']);
        echo Html::button('<i class="fas fa-minus"></i>', ['class' => 'js_e_qty_remove qty-remove btn btn-default', 'data' => ['for' => $meal->id]]);
        echo Html::endTag('span');
        echo Html::input('number', $meal->id, '0', ['id' => 'e_product-quantity-input_34567367', 'min' => 0, 'step' => 1, 'class' => 'e_product-quantity-input hidden']);
        echo Html::tag('label', 0, ['class' => 'js_e_qty e_qty_addon form-control m-0 text-center', 'data' => ['for' => $meal->id, 'unit' => ' шт.']]);
        echo Html::beginTag('span', ['class' => 'input-group-btn']);
        echo Html::button('<i class="fas fa-plus"></i>', ['class' => 'js_e_qty_add qty-add btn btn-default', 'data' => ['for' => $meal->id]]);
        echo Html::endTag('span');
        echo Html::endTag('div');
        echo Html::tag('div', 'Добавить', ['class' => 'js_e_qty_toggle e_btn-qty-toggle btn btn-lg btn-warning w-100', 'data' => ['for' => $meal->id]]);
        echo Html::endTag('div');
        echo Html::tag('div', Html::tag('p', $price[0] . '<small>,' . $price[1] . '</small> &#8381;/шт.', ['class' => 'e_price-badge']), ['class' => 'col-xs-6']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'mb-5']);
        echo Html::beginTag('p', ['class' => (strlen($meal->description) < 200 ? 'lead' : '')]);
        echo Html::encode($meal->description);
        echo Html::endTag('p');
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'mb-5']);
        echo Html::tag('h3', 'Состав', ['class' => 'e_title-muted']);
        echo Html::beginTag('ul', ['class' => 'list-unstyled lead']);
        foreach ($meal->mealProducts as $mealProduct) {
            echo Html::beginTag('li', ['class' => 'mb-2']);
            echo Html::encode($mealProduct->product) . ' — <em class="text-muted">' . Html::encode((float)$mealProduct->product_quantity) . ' ' . Html::encode($mealProduct->unit->name) . '</em>';
            echo Html::endTag('li');
        }
        echo Html::endTag('ul');
        echo Html::endTag('div');

        Modal::end();
        /*  END | Product modal  */
    }

    echo Html::beginTag('nav', ['class' => 'container-fluid e_pagination text-center', 'aria-label' => 'Page navigation']);
    echo Html::beginTag('ul', ['class' => 'pagination pagination-lg my-2']);

    echo Html::beginTag('li');
    echo Html::a(Html::tag('span', '&laquo;', ['aria-hidden' => true]), '#', ['aria-label' => 'Previous']);
    echo Html::endTag('li');

    echo Html::beginTag('li');
    echo Html::endTag('li');

    echo Html::beginTag('li');
    echo Html::a('1', '#');
    echo Html::endTag('li');

    echo Html::beginTag('li', ['class' => 'active']);
    echo Html::a('2', '#');
    echo Html::endTag('li');

    echo Html::beginTag('li');
    echo Html::tag('span', '...');
    echo Html::endTag('li');

    echo Html::beginTag('li');
    echo Html::a('128', '#');
    echo Html::endTag('li');

    echo Html::beginTag('li');
    echo Html::a(Html::tag('span', '&raquo;', ['aria-hidden' => true]), '#', ['aria-label' => 'Next']);
    echo Html::endTag('li');

    echo Html::endTag('ul');
    echo Html::endTag('nav');
}
