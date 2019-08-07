<?php

/* @var $this View */
/* @var $content string */

use backend\assets\AppAsset;
use backend\controllers\document\RequestController;
use backend\controllers\reference\ChildController;
use backend\controllers\reference\ComplexController;
use backend\controllers\reference\ContractController;
use backend\controllers\reference\EmployeeController;
use backend\controllers\reference\FatherController;
use backend\controllers\reference\FileController;
use backend\controllers\reference\MealController;
use backend\controllers\reference\MenuController;
use backend\controllers\reference\ProductCategoryController;
use backend\controllers\reference\ProductController;
use backend\controllers\reference\ProductProviderController;
use backend\controllers\reference\SchoolClassController;
use backend\controllers\reference\ServiceObjectController;
use backend\controllers\reference\UnitController;
use backend\controllers\reference\UserController;
use backend\controllers\report\TasksController;
use backend\controllers\system\ImportServiceObjectAndContractController;
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
                            'label' => 'Поставщики продуктов',
                            'url' => ['/reference/product-provider/index'],
                            'visible' => Yii::$app->user->can(ProductProviderController::class . '.Index'),
                        ],
                        [
                            'label' => 'Объекты обслуживания',
                            'url' => ['/reference/service-object/index'],
                            'visible' => Yii::$app->user->can(ServiceObjectController::class . '.Index'),
                        ],
                        [
                            'label' => 'Сотрудники',
                            'url' => ['/reference/employee/index'],
                            'visible' => Yii::$app->user->can(EmployeeController::class . '.Index'),
                        ],
                        [
                            'label' => 'Договора с объектами обслуживания',
                            'url' => ['/reference/contract/index'],
                            'visible' => Yii::$app->user->can(ContractController::class . '.Index'),
                        ],
                        [
                            'label' => 'Классы',
                            'url' => ['/reference/school-class/index'],
                            'visible' => Yii::$app->user->can(SchoolClassController::class . '.Index'),
                        ],
                        [
                            'label' => 'Родители',
                            'url' => ['/reference/father/index'],
                            'visible' => Yii::$app->user->can(FatherController::class . '.Index'),
                        ],
                        [
                            'label' => 'Дети',
                            'url' => ['/reference/child/index'],
                            'visible' => Yii::$app->user->can(ChildController::class . '.Index'),
                        ],
                        [
                            'label' => 'Единицы измерения',
                            'url' => ['/reference/unit/index'],
                            'visible' => Yii::$app->user->can(UnitController::class . '.Index'),
                        ],
                        [
                            'label' => 'Категории продуктов',
                            'url' => ['/reference/product-category/index'],
                            'visible' => Yii::$app->user->can(ProductCategoryController::class . '.Index'),
                        ],
                        [
                            'label' => 'Продукты',
                            'url' => ['/reference/product/index'],
                            'visible' => Yii::$app->user->can(ProductController::class . '.Index'),
                        ],
                        [
                            'label' => 'Блюда',
                            'url' => ['/reference/meal/index'],
                            'visible' => Yii::$app->user->can(MealController::class . '.Index'),
                        ],
                        [
                            'label' => 'Комплексы',
                            'url' => ['/reference/complex/index'],
                            'visible' => Yii::$app->user->can(ComplexController::class . '.Index'),
                        ],
                        [
                            'label' => 'Меню',
                            'url' => ['/reference/menu/index'],
                            'visible' => Yii::$app->user->can(MenuController::class . '.Index'),
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
                            'label' => 'Импорт объектов обслуживания и договоров',
                            'url' => ['/system/import-service-object-and-contract/index'],
                            'visible' => Yii::$app->user->can(ImportServiceObjectAndContractController::class . '.Index'),
                        ],
                    ],
                ],
            ];
            $menuItems[] = '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Выход (' . Yii::$app->user->identity->name_full . ')',
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
