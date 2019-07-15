<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
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
                    'label' => 'Главная',
                    'url' => ['/site/index']
                ],
                [
                    'label' => 'Документы',
                    'items' => [
                        [
                            'label' => 'Предварительная заявка',
                            'url' => ['/document/preliminary-request/index'],
                        ],
                        [
                            'label' => 'Корректировка заявки',
                            'url' => ['/document/correction-request/index'],
                        ],
                    ],
                ],
                [
                    'label' => 'Справочники',
                    'items' => [
                        [
                            'label' => 'Пользователи',
                            'url' => ['/reference/user/index'],
                        ],
                        [
                            'label' => 'Единицы измерения',
                            'url' => ['/reference/unit/index'],
                        ],
                        [
                            'label' => 'Продукты',
                            'url' => ['/reference/product/index'],
                        ],
                        [
                            'label' => 'Контрагенты',
                            'url' => ['/reference/contractor/index'],
                        ],
                        [
                            'label' => 'Договоры с контрагентами',
                            'url' => ['/reference/contract/index'],
                        ],
                    ],
                ],
                [
                    'label' => 'Администрирование',
                    'items' => [
                        [
                            'label' => 'Права доступа',
                            'url' => ['/system/role/index'],
                        ],
                    ],
                ],
            ];
            $menuItems[] = '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Выход (' . Yii::$app->user->identity->name . ')',
                    ['class' => 'btn btn-link logout']
                )
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
