<?php

/* @var $this View */

/* @var $content string */

use common\models\enum\FoodType;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use yii\helpers\Html;
use terminal\assets\AppAsset;
use yii\web\View;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php

//        README
//        К .js_e_cart_preview Нужно добавить класс active, если в корзине есть товары и убирать его, если она пуста
//
//        В .badge нужно писать количество товара, если оно есть. Если корзина пуста - то и этот элемент должен быть пуст - тогда он скроется автоматически (css)
//        <span class="badge">6</span> - будет виден
//        <span class="badge"></span> - исчезнет

$this->beginBody();

/** @var MealCategory[] $categories */
$categories = MealCategory::find()
    ->alias('mc')
    ->innerJoin(Meal::tableName() . ' AS m', 'mc.id = m.meal_category_id')
    ->andWhere([
        'mc.is_active' => true,
        'm.is_active' => true,
        'm.food_type_id' => FoodType::BUFFET
    ])
    ->all();

$categoryId = Yii::$app->request->get('categoryId') ?? null;
$active = !empty($session['meals']) ? 'active' : '';

echo Html::beginTag('div', ['class' => 'topbar container-fluid pt-3']);
echo Html::tag('div', Html::tag('span', Yii::$app->view->title, ['class' => 'mb-0 pl-3 ellipsis']), ['class' => 'category-title']);
echo Html::beginTag('div', ['class' => 'js_e_cart_preview  ' . $active . ' e_cart_preview  text-right h-100']);
// Кнопка "Отменить"
echo Html::a(Html::tag('i', '', ['class' => 'fas fa-times-circle mr-2']) . 'Отменить',
    '#', ['class' => 'js_e_reset wf-cart-btn-reset btn btn-lg ml-3']);
// Кнопка "Корзина"
echo Html::a(Html::tag('i', '', ['class' => 'fas fa-shopping-cart mr-2']) . 'Корзина ' . Html::tag('span', 'пуста ', ['class' => 'empty-only'])
    . Html::tag('span', (!empty($session['meals']) ? count($session['meals']) : ''), ['class' => 'badge']),
    ['cart-form/index'], ['class' => 'wf-cart-btn-cart btn btn-lg ml-3 h-100']);
// Кнопка "Оплатить"
echo Html::a(Html::tag('i', '', ['class' => 'fas fa-money-bill-wave mr-2']) . 'Оплатить '
    . Html::tag('span', Html::tag('span', '153<small>,50</small>', ['class' => 'js_e_sum']) . ' &#8381', ['class' => 'price']),
    '#', ['class' => 'wf-cart-btn-checkout btn btn-lg ml-2 h-100']);
echo Html::endTag('div');
echo Html::endTag('div');


echo Html::beginTag('div', ['class' => 'left-sidebar']);
echo Html::tag('div', '', ['class' => 'wf-js-fade-bg wf-js-sidebar-close']);
echo Html::beginTag('ul', ['class' => 'list-unstyled']);

echo Html::beginTag('li', ['class' => 'logo sidebar-item']);
$logo = Html::tag('div', '', ['class' => 'icon logo-icon bg-contain', 'style' => 'background-image: url(../img/logo_color.svg);']);
$logo .= Html::tag('span', 'ВебЕда', ['class' => 'menu-title py-0']);
echo Html::a($logo, ['site/index']);
echo Html::endTag('li');

echo Html::beginTag('li', ['class' => 'sidebar-item wf-js-sidebar-expand-btn e-border-bottom']);
echo Html::tag('div', '', ['class' => 'icon fas fa-fw fa-bars']);
echo Html::tag('span', '<small>Закрыть меню</small>', ['class' => 'menu-title']);
echo Html::endTag('li');

echo '<br>';

echo Html::beginTag('li', ['class' => 'sidebar-item' . ($categoryId == 'complexes' ? ' active' : '')]);
$logo = Html::tag('div', '', ['class' => 'icon fas fa-fw fa-th-list']);
$logo .= Html::tag('span', 'Комплексы', ['class' => 'menu-title']);
echo Html::a($logo, ['index', 'categoryId' => 'complexes'], ['method' => 'post']);
echo Html::endTag('li');

foreach ($categories as $category) {
    echo Html::beginTag('li', ['class' => 'sidebar-item ' . ($categoryId == $category->id ? ' active' : '')]);
    $logo = Html::tag('div', '', ['class' => 'icon fas fa-fw fa-coffee']);
    $logo .= Html::tag('span', Html::encode($category), ['class' => 'menu-title']);
    echo Html::a($logo, ['site/index', 'categoryId' => $category->id]);
    echo Html::endTag('li');
}

echo Html::endTag('ul');
echo Html::endTag('div');

echo '<div class="container-fluid py-4 px-4">';
echo $content;
echo '</div>';

$this->endBody();

?>
</body>
</html>
<?php $this->endPage() ?>
