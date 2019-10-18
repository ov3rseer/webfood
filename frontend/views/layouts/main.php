<?php

/* @var $this View */
/* @var $content string */

/* @throws Exception */

use common\models\enum\ContractType;
use common\models\enum\UserType;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$this->beginPage();

$this->registerJs("
    $().ready(function() {
   
        var navbarFluent = new FluentUI({
            '#navbar' : {
                'mouseover' : function() {
                    let el = $('#signup-link-btn, #login-link-btn, #profile-link-btn, #logout-submit-btn, #navbar');
                    el.addClass('highlight'); 
                },
                'mouseout' : function() {
                    let el = $('#signup-link-btn, #login-link-btn, #profile-link-btn, #logout-submit-btn, #navbar');
                    el.removeClass('highlight'); 
                }
            },
            '#signup-link-btn' : {
                'mouseover focus' : function() {
                    let el = $('#signup-link-btn');
                    el.addClass('hover'); 
                },
                'mouseout blur' : function() {
                    let el = $('#signup-link-btn');
                    el.removeClass('hover'); 
                }
            },
            '#login-link-btn' : {
                'mouseover focus' : function() {
                    let el = $('#login-link-btn');
                    el.addClass('hover'); 
                },
                'mouseout blur' : function() {
                    let el = $('#login-link-btn');
                    el.removeClass('hover'); 
                }
            },
            '#profile-link-btn' : {
                'mouseover focus' : function() {
                    let el = $('#profile-link-btn');
                    el.addClass('hover'); 
                },
                'mouseout blur' : function() {
                    let el = $('#profile-link-btn');
                    el.removeClass('hover'); 
                }
            },
            '#logout-submit-btn' : {
                'mouseover focus' : function() {
                    let el = $('#logout-submit-btn');
                    el.addClass('hover'); 
                },
                'mouseout blur' : function() {
                    let el = $('#logout-submit-btn');
                    el.removeClass('hover'); 
                }
            },
            '#login-link-btn, #signup-link-btn, #profile-link-btn, #logout-submit-btn' : {
                'focus' : function() {
                    let el = $('#navbar');
                    el.addClass('highlight'); 
                },
                'blur' : function() {
                    let el = $('#navbar');
                    el.removeClass('highlight'); 
                }
            }
        });
    });
");

?>
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

    <nav class="hidden-navbar navbar-sticky-top" role="navigation" id="navbar">
        <div class="container">
            <div class="navbar-header">
                <?= Html::a(Yii::$app->name, Yii::$app->homeUrl, ['class' => 'hidden-link-lg navbar-brand']) ?>
            </div>
            <div class="navbar-form navbar-right">

                <?php
                if (Yii::$app->user->isGuest) {
                    echo Html::a('Регистрация', ['site/signup'], ['id' => 'signup-link-btn', 'class' => 'hidden-btn']);
                    echo Html::a('Вход', ['site/login'], ['id' => 'login-link-btn', 'class' => 'hidden-btn']);
                } else {
                    switch (Yii::$app->user->identity->user_type_id) {
                        case UserType::ADMIN:
                            echo Html::a('Админ-панель', 'admin', ['class' => 'hidden-btn']);
                            break;
                        case UserType::SERVICE_OBJECT:
                            echo Html::beginTag('div', ['class' => 'btn-group']);
                            echo Html::button('Ученики <span class="caret"></span>', [
                                'class' => 'btn btn-default dropdown-toggle',
                                'data-toggle' => 'dropdown',
                            ]);
                            echo Html::beginTag('ul', ['class' => 'dropdown-menu', 'role' => 'menu']);
                            echo '<li>' . Html::a('Сотрудники', null, ['class' => 'hidden-btn']) . '</li>';
                            echo '<li>' . Html::a('Ученики', ['serviceObject/children-introduction/index'], [
                                    'class' => 'hidden-btn',
                                ]) . '</li>';
                            echo Html::endTag('ul');
                            echo Html::endTag('div');

                            echo Html::beginTag('div', ['class' => 'btn-group']);
                            echo Html::button('Заявки <span class="caret"></span>', [
                                'class' => 'btn btn-default dropdown-toggle',
                                'data-toggle' => 'dropdown',
                            ]);
                            echo Html::beginTag('ul', ['class' => 'dropdown-menu', 'role' => 'menu']);
                            echo '<li>' . Html::a('Предварительная заявка', null, [
                                    'id' => 'preliminary-request',
                                    'class' => 'hidden-btn',
                                    'data-action' => 'serviceObject/request/index',
                                ]) . '</li>';
                            echo '<li>' . Html::a('Корректировка заявки', null, [
                                    'id' => 'correction-request',
                                    'class' => 'hidden-btn',
                                    'data-action' => 'serviceObject/request/index',
                                ]) . '</li>';
                            echo Html::endTag('ul');
                            echo Html::endTag('div');



                            echo Html::beginTag('div', ['class' => 'btn-group']);
                            echo Html::button('Столовая <span class="caret"></span>', [
                                'class' => 'btn btn-default dropdown-toggle',
                                'data-toggle' => 'dropdown',
                            ]);
                            echo Html::beginTag('ul', ['class' => 'dropdown-menu', 'role' => 'menu']);
                            echo '<li>' . Html::a('Меню', null, ['class' => 'hidden-btn']) . '</li>';
                            echo '<li>' . Html::a('Категории продуктов', ['serviceObject/product-category/index'], ['class' => 'hidden-btn']) . '</li>';
                            echo '<li>' . Html::a('Продукты', ['serviceObject/product/index'], ['class' => 'hidden-btn']) . '</li>';
                            echo '<li>' . Html::a('Категории блюд', ['serviceObject/meal-category/index'], ['class' => 'hidden-btn']) . '</li>';
                            echo '<li>' . Html::a('Блюда', null, ['class' => 'hidden-btn']) . '</li>';
                            echo '<li>' . Html::a('Комплексы', null, ['class' => 'hidden-btn']) . '</li>';
                            echo Html::endTag('ul');
                            echo Html::endTag('div');
                            break;
                        case UserType::FATHER:
                            break;
                        case UserType::EMPLOYEE:
                            break;
                        case UserType::PRODUCT_PROVIDER:
                            break;
                        default:
                            return $this->render('index');
                    }

                    echo Html::a(Yii::$app->user->identity->name_full ?? Yii::$app->user->identity->name, ['user/profile/index'], [
                        'id' => 'profile-link-btn',
                        'class' => 'hidden-btn'
                    ]);
                    echo Html::beginForm(['/site/logout'], 'post', ['style' => 'display: inline-block;']);
                    echo Html::submitButton('Выход', [
                        'id' => 'logout-submit-btn',
                        'class' => 'hidden-btn'
                    ]);
                    echo Html::endForm();
                }
                ?>
            </div>
        </div>
    </nav>
    <?php
    $modalId = 'choice_contract_type_for_request';
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
            var url = this.attributes['data-action'].value;
            var action = this.id;
            $('#" . $modalId . " a').each(function() {
                var contractTypeId = this.attributes['data-contract-type'].value;
                this.href = location.origin + '/' + url + '?contractTypeId=' + contractTypeId + '&action=' + action;
            });
            $('#" . $modalId . "').modal('show');
        });"
    );
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
