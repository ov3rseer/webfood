<?php

/* @var $this View */

/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\View;
use yii\widgets\Breadcrumbs;
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
        $menuItems[] = ['label' => 'Вход', 'url' => ['/site/login']];
    } else {
        $menuItems = [
            [
                'label' => 'Документы',
                'visible' => Yii::$app->user->can('super-admin'),
                'items' => [
                    [
                        'label' => 'Заявки',
                        'url' => ['/document/request/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Покупки',
                        'url' => ['/document/purchase/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Пополнения карт',
                        'url' => ['/document/refill-balance/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                ],
            ],
            [
                'label' => 'Справочники',
                'visible' => Yii::$app->user->can('super-admin'),
                'items' => [
                    [
                        'label' => 'Пользователи',
                        'url' => ['/reference/user/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Поставщики продуктов',
                        'url' => ['/reference/product-provider/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Объекты обслуживания',
                        'url' => ['/reference/service-object/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Сотрудники',
                        'url' => ['/reference/employee/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Договора с объектами обслуживания',
                        'url' => ['/reference/contract/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Классы',
                        'url' => ['/reference/school-class/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Родители',
                        'url' => ['/reference/father/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Дети',
                        'url' => ['/reference/child/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Карты детей',
                        'url' => ['/reference/card-child/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Единицы измерения',
                        'url' => ['/reference/unit/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Категории продуктов',
                        'url' => ['/reference/product-category/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Продукты',
                        'url' => ['/reference/product/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Блюда',
                        'url' => ['/reference/meal/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Категории блюд',
                        'url' => ['/reference/meal-category/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Комплексы',
                        'url' => ['/reference/complex/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Меню',
                        'url' => ['/reference/menu/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Установка меню',
                        'url' => ['/reference/set-menu/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                ],
            ],
            [
                'label' => 'Администрирование',
                'visible' => Yii::$app->user->can('super-admin'),
                'items' => [
                    [
                        'label' => 'Настройки системы',
                        'url' => ['/reference/system-setting/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Файлы',
                        'url' => ['/reference/file/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Задачи',
                        'url' => ['/report/tasks'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Импорт поставщиков продуктов',
                        'url' => ['/system/import-product-provider/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                    [
                        'label' => 'Импорт объектов обслуживания',
                        'url' => ['/system/import-service-object/index'],
                        'visible' => Yii::$app->user->can('super-admin'),
                    ],
                ],
            ],
        ];
        $menuItems[] = '<li class="ml-5">'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton('Выход (' . Yii::$app->user->identity->name_full . ')', ['class' => 'btn btn-link'])
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
