<?php

/* @var $this View */
/* @var $content string */

use backend\assets\AppAsset;
use backend\controllers\document\RequestController;
use backend\controllers\reference\ContractController;
use backend\controllers\reference\ContractorController;
use backend\controllers\reference\FileController;
use backend\controllers\reference\ProductController;
use backend\controllers\reference\UnitController;
use backend\controllers\reference\UserController;
use backend\controllers\report\TasksController;
use backend\controllers\system\ImportContractorAndContractController;
use backend\controllers\system\RoleController;
use common\models\reference\SystemSetting;
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
                    'label' => 'Главная',
                    'url' => ['/site/index']
                ],
                [
                    'label' => 'Документы',
                    'items' => [
                        [
                            'label' => 'Заявка',
                            'url' => ['/document/request/index'],
                            'visible' => Yii::$app->user->can( RequestController::class . '.Index'),
                        ],
                    ],
                ],
                [
                    'label' => 'Справочники',
                    'items' => [
                        [
                            'label' => 'Пользователи',
                            'url' => ['/reference/user/index'],
                            'visible' => Yii::$app->user->can(UserController::class . '.Index'),
                        ],
                        [
                            'label' => 'Файлы',
                            'url' => ['/reference/file/index'],
                            'visible' => Yii::$app->user->can(FileController::class . '.Index'),
                        ],
                        [
                            'label' => 'Единицы измерения',
                            'url' => ['/reference/unit/index'],
                            'visible' => Yii::$app->user->can(UnitController::class . '.Index'),
                        ],
                        [
                            'label' => 'Продукты',
                            'url' => ['/reference/product/index'],
                            'visible' => Yii::$app->user->can(ProductController::class . '.Index'),
                        ],
                        [
                            'label' => 'Контрагенты',
                            'url' => ['/reference/contractor/index'],
                            'visible' => Yii::$app->user->can(ContractorController::class . '.Index'),
                        ],
                        [
                            'label' => 'Договоры с контрагентами',
                            'url' => ['/reference/contract/index'],
                            'visible' => Yii::$app->user->can(ContractController::class . '.Index'),
                        ],
                    ],
                ],
                [
                    'label' => 'Администрирование',
                    'items' => [
                        [
                            'label' => 'Настройки системы',
                            'url' => ['/reference/system-setting/index'],
                            'visible' => Yii::$app->user->can(SystemSetting::class . '.Index'),
                        ],
                        [
                            'label' => 'Задачи',
                            'url'   => ['/report/tasks'],
                            'visible' => Yii::$app->user->can(TasksController::class . '.Index'),
                        ],
                        [
                            'label' => 'Права доступа',
                            'url' => ['/system/role/index'],
                            'visible' => Yii::$app->user->can(RoleController::class . '.Index'),
                        ],
                        [
                            'label' => 'Импорт контрагентов и договоров',
                            'url' => ['/system/import-contractor-and-contract/index'],
                            'visible' => Yii::$app->user->can(ImportContractorAndContractController::class . '.Index'),
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
