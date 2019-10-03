<?php

/* @var  yii\web\View $this */
/* @var  Form $model */

/* @var  integer $categoryId */

use common\models\enum\MealType;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use common\models\form\Form;
use yii\bootstrap\Modal;
use yii\helpers\Html;

$this->title = 'Terminal WebFood';

if (isset($categoryId)) {
    $category = MealCategory::findOne(['id' => $categoryId]);

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

        echo Html::beginTag('div', ['class' => 'col-xs-4 px-4 py-3 h-3v']);
        echo Html::beginTag('div', ['class' => 'e_product e_product-card rounded shadow embed-responsive h-100', 'data' => ['e_product_id' => 34567367]]);

        echo Html::beginTag('div', ['class' => 'embed-responsive-item icon-bg']);
        echo Html::tag('i', '', ['class' => 'icon fas fa-th-list mr-2']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'js_e_qty_toggle  card-body embed-responsive-item p-4', 'data' => ['for' => 34567367]]);

        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('p', ['class' => 'subtitle mt-0 mb-2 col-xs-7']);
        echo Html::tag('span', Html::tag('i', '', ['class' => 'icon fas fa-th-list mr-2']) . Html::encode($meal->mealCategory), ['class' => 'ellipsis']);
        echo Html::endTag('p');
        echo Html::endTag('div');

        /* Price */
        echo Html::tag('h2', Html::encode($meal), ['class' => 'card-title mt-0 mb-2']);
        echo Html::beginTag('p', ['class' => 'card-text pb-1']);
        echo Html::tag('span', Html::encode($meal->price) . ' 64<small>,50</small> &#8381;/шт.', ['class' => 'e_price-badge float-left']);
        echo Html::endTag('p');
        echo Html::endTag('div');
        /* END | Price */

        /* Button trigger modal */
        echo Html::button(Html::tag('i', '', ['class' => 'fas fa-info-circle mr-2']) . 'Подробнее',
            ['class' => 'card-addon pt-4 pr-4 pl-3 pb-3', 'data' => ['toggle' => 'modal', 'target' => '#myModal_34567367']]);
        /* END | Button trigger modal */

        /* Контролы количества */
        echo Html::beginTag('div', ['class' => 'card-footer text-center px-0 pt-3 m-0 pb-0']);
        echo Html::beginTag('div', ['class' => 'on-active row']);

        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo Html::button('', ['class' => 'js_e_qty_remove btn-qty qty-remove', 'data' => ['for' => 34567367]]);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo Html::input('number', '34567367', 0, ['class' => 'e_product-quantity-input hidden', 'min' => 0, 'step' => 1]);
        echo Html::tag('label', 0, ['class' => 'js_e_qty e_qty_addon m-0', 'data' => ['for' => 34567367, 'unit' => ' шт.']]);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-xs-4']);
        echo Html::button('', ['class' => 'js_e_qty_add btn-qty qty-add', 'data' => ['for' => 34567367]]);
        echo Html::endTag('div');


        echo Html::endTag('div');
        echo Html::endTag('div');
        /*  END | Контролы количества  */

        echo Html::endTag('div');
        echo Html::endTag('div');

        /*  Product modal  */
        Modal::begin(['options' => [
            'id' => 'myModal_34567367',
            'class' => 'e_modal modal fade',
            'tabindex' => '-1',
            'role' => 'dialog',
            'aria-labelledby' => 'myModalLabel',
            'data' => [
                'qty' => 0,
                'price' => '64<small>,50</small>',
                'e_product_id' => 34567367,
            ]],
            'bodyOptions' => ['class' => 'modal-body p-5'],
            'closeButton' => ['class' => 'close'],
            'header' => Html::beginTag('p', ['class' => 'subtitle mt-0 mb-2']) .
                Html::tag('i', '', ['class' => 'icon fas fa-th-list mr-2']) . 'Комплекс'
                . Html::tag('h2', 'Комплекс "Завтрак', ['class' => 'modal-title'])
                . Html::endTag('p'),
            'headerOptions' => ['class' => 'modal-header px-5 pt-5'],
        ]);

//        echo Html::beginTag('button', ['type' => 'button', 'class' => 'close e_close_modal', 'aria-label', 'data' => ['dismiss' => 'modal']]);
//        echo Html::beginTag('span', ['aria-hidden' => 'true']);
//        echo Html::tag('div', '&times;', ['class' => 'btn']);
//        echo Html::endTag('span');
//        echo Html::endTag('button');


        echo Html::beginTag('div', ['class' => 'row price-and-qty mb-5']);
        echo Html::beginTag('div', ['class' => 'col-xs-6 e_qty-controls']);
        echo Html::beginTag('div', ['class' => 'input-group input-group-lg']);

        echo Html::beginTag('span', ['class' => 'input-group-btn']);
        echo Html::button('<i class="fas fa-minus"></i>', ['class' => 'js_e_qty_remove qty-remove btn btn-default', 'data' => ['for' => 34567367]]);
        echo Html::endTag('span');

        echo Html::input('number', 34567367, '0', ['id' => 'e_product-quantity-input_34567367', 'min' => 0, 'step' => 1, 'class' => 'e_product-quantity-input hidden']);
        echo Html::tag('label', 0, ['class' => 'js_e_qty e_qty_addon form-control m-0 text-center', 'data' => ['for' => 34567367, 'unit' => ' шт.']]);
        echo Html::beginTag('span', ['class' => 'input-group-btn']);
        echo Html::button('<i class="fas fa-plus"></i>', ['class' => 'js_e_qty_add qty-add btn btn-default', 'data' => ['for' => 34567367]]);
        echo Html::endTag('span');
        echo Html::endTag('div');
        echo Html::tag('div', 'Добавить', ['class' => 'js_e_qty_toggle e_btn-qty-toggle btn btn-lg btn-warning w-100', 'data' => ['for' => 34567367]]);
        echo Html::endTag('div');
        echo Html::tag('div', Html::tag('p', '64<small>,50</small> &#8381;/шт.', ['class' => 'e_price-badge']), ['class' => 'col-xs-6']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'mb-5']);
        echo Html::beginTag('p', ['class' => 'lead']);
        echo 'Описание крупным размером шрифта. Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
        echo Html::endTag('p');
        echo Html::beginTag('p', ['class' => '']);
        echo 'Описание обычным размером шрифта. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
        echo Html::endTag('p');
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'mb-5']);
        echo Html::tag('h3', 'Состав', ['class' => 'e_title-muted']);
        echo Html::beginTag('ul', ['class' => 'list-unstyled lead']);
        echo Html::beginTag('li', ['class' => 'mb-2']);
        echo 'Что-нибудь — <em class="text-muted">1 шт</em>';
        echo Html::endTag('li');
        echo Html::beginTag('li', ['class' => 'mb-2']);
        echo 'Что-то ещё — <em class="text-muted">250 г</em>';
        echo Html::endTag('li');
        echo Html::beginTag('li', ['class' => 'mb-2']);
        echo 'Ещё какой-нибудь продукт — <em class="text-muted">50 г</em>';
        echo Html::endTag('li');

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
