<?php

/* @var $this View */
/* @var $content string */

/* @throws Exception */

use common\models\enum\UserType;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

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
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'innerContainerOptions' => ['class' => 'container-fluid'],
    ]);
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Регистрация', 'url' => ['site/signup'], ['id' => 'signup-link-btn', 'class' => 'hidden-btn']];
        $menuItems[] = ['label' => 'Вход', 'url' => ['/site/login'], ['id' => 'login-link-btn', 'class' => 'hidden-btn']];
    } else {
        switch (Yii::$app->user->identity->user_type_id) {

            case UserType::ADMIN:
                Html::a('Админ-панель', 'admin', ['class' => 'hidden-btn']);
                $menuItems[] = [
                    'label' => 'Админ-панель',
                    'url' => ['/admin'],
                    'visible' => Yii::$app->user->can('super-admin'),
                ];
                break;

            case UserType::SERVICE_OBJECT:
                $menuItems = [
                    [
                        'label' => 'Заявки',
                        'url' => Url::to(['serviceObject/request/index']),
                        'visible' => Yii::$app->user->can('service-object'),
                    ],
//                    [
//                        'label' => 'Сотрудники',
//                        'url' => [''],
//                        'visible' => Yii::$app->user->can('service-object'),
//                    ],
                    [
                        'label' => 'Ученики',
                        'url' => ['/serviceObject/children-introduction/index'],
                        'visible' => Yii::$app->user->can('service-object'),
                    ],
                    [
                        'label' => 'Столовая',
                        'visible' => Yii::$app->user->can('service-object'),
                        'items' => [
                            [
                                'label' => 'Категории блюд',
                                'url' => ['/serviceObject/meal-category/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
                            [
                                'label' => 'Блюда',
                                'url' => ['/serviceObject/meal/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
                            [
                                'label' => 'Комплексы',
                                'url' => ['/serviceObject/complex/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
                            [
                                'label' => 'Меню',
                                'url' => ['/serviceObject/menu/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
                            [
                                'label' => 'Установка меню и выходных дней',
                                'url' => ['/serviceObject/set-menu/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
                        ],
                    ],
                ];
                break;

            case UserType::OTHER:
            case UserType::EMPLOYEE:
            case UserType::PRODUCT_PROVIDER:
                $menuItems = [
                    [
                        'label' => 'Продукты',
                        'url' => ['/productProvider/product/index'],
                        'visible' => Yii::$app->user->can('product-provider'),
                    ],
                ];
                break;
            case UserType::FATHER:
                break;
        }

        $menuItems[] = '<li class="ml-5">' .
            Html::a(Yii::$app->user->identity->name_full ?? Yii::$app->user->identity->name,
                ['/user/profile/index'], ['class' => 'btn btn-link'])
            . '</li>';
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton('Выход', ['class' => 'btn btn-link logout'])
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Alert::widget() ?>
        <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
        <?= $content ?>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
