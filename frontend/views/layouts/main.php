<?php

/* @var $this View */
/* @var $content string */

/* @throws Exception */

use common\models\enum\ContractType;
use common\models\enum\UserType;
use yii\bootstrap\Modal;
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
    $modalId = 'choose_contract_type_for_request';
    Modal::begin([
        'options' => [
            'id' => $modalId,
        ],
        'header' => 'Выберите тип договора',
    ]);
    echo Html::a('Дети', null, [
        'class' => 'btn btn-lg btn-success btn-block',
        'data-contract-type' => ContractType::CHILD,
    ]);
    echo Html::a('Сотрудники', null, [
        'class' => 'btn btn-lg btn-success btn-block',
        'data-contract-type' => ContractType::EMPLOYEES,
    ]);
    Modal::end();

    $this->registerJs(" 
        $('#preliminary-request, #correction-request').click(function(e) {
            e.preventDefault();
            var action = this.id;
            $('#" . $modalId . " a').each(function(){
                var contractTypeId = $(this).attr('data-contract-type');
                this.href =  '" . Url::to(['serviceObject/request/index']) . "' + '?contractTypeId=' + contractTypeId + '&action=' + action;
            });
            $('#" . $modalId . "').modal('show');
        });"
    );

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
                        'visible' => Yii::$app->user->can('service-object'),
                        'items' => [
                            [
                                'label' => 'Предварительная заявка',
                                'url' => ['#'],
                                'visible' => Yii::$app->user->can('service-object'),
                                'linkOptions' => [
                                    'id' => 'preliminary-request',
                                ]
                            ],
                            [
                                'label' => 'Корректировка заявки',
                                'url' => ['#'],
                                'visible' => Yii::$app->user->can('service-object'),
                                'linkOptions' => [
                                    'id' => 'correction-request',
                                ]
                            ],
                        ],
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
                                'label' => 'Категории продуктов',
                                'url' => ['/serviceObject/product-category/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
                            [
                                'label' => 'Продукты',
                                'url' => ['/serviceObject/product/index'],
                                'visible' => Yii::$app->user->can('service-object'),
                            ],
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
