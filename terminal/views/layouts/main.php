<?php

/* @var $this View */

/* @var $content string */

use common\models\enum\MealType;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use terminal\assets\AppAsset;
use common\widgets\Alert;
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

$this->beginBody();
$session = Yii::$app->session;
if (!empty($session['meals'])) {
    $menuItems[] = Html::a('ОТМЕНИТЬ ПОКУПКУ <span class="glyphicon glyphicon-remove"></span>', ['cart-form/delete-all-meals'], ['class' => 'btn btn-danger']);
}
$menuItems[] = Html::a('КОРЗИНА <span class="glyphicon glyphicon-shopping-cart"></span>  
<span class="badge">' . (!empty($session['meals']) ? count($session['meals']) : 0) . '</span>', ['cart-form/index'], ['class' => 'btn btn-success']);

NavBar::begin([
    'brandLabel' => Yii::$app->name,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-sticky-top',
    ],
]);
echo Nav::widget(['options' => ['class' => 'navbar-nav navbar-right'], 'items' => $menuItems]);
NavBar::end();


/** @var MealCategory $category */
$categories = MealCategory::find()
    ->alias('mc')
    ->innerJoin(Meal::tableName() . ' AS m', 'mc.id = m.meal_category_id')
    ->andWhere([
        'mc.is_active' => true,
        'm.is_active' => true,
        'm.meal_type_id' => MealType::BUFFET_MEALS
    ])
    ->all();

echo Html::beginTag('div', ['style' => 'position: relative;']);
echo Html::beginTag('div', ['class' => 'left-sidebar']);
echo Html::tag('h3', 'Категории блюд');
foreach ($categories as $category) {
    echo Html::a(Html::encode($category), ['site/index', 'categoryId' => $category->id], ['title' => Html::encode($category), 'class' => 'btn btn-success']);
}
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'right-placeholder']);
echo Html::beginTag('div', ['class' => 'container-fluid']);
echo Alert::widget();
echo $content;
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::endTag('div');

$this->endBody();

?>
</body>
</html>
<?php $this->endPage() ?>
