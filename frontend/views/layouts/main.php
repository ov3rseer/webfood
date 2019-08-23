<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @throws \Exception */

use yii\helpers\Html;
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
                <a href="/site/index" class="hidden-link-lg navbar-brand"><?= Yii::$app->name ?></a>
            </div>
            <div class="navbar-form navbar-right">
                <?php
                if (Yii::$app->user->isGuest) {
                    ?>
                    <button id="signup-link-btn" class="hidden-btn" onclick="location.href = '/site/signup'">Регистрация</button>
                    <button id="login-link-btn" class="hidden-btn" onclick="location.href = '/site/login'">Вход</button>
                    <?php
                } else {
                    ?>
                    <button id="profile-link-btn" class="hidden-btn" onclick="location.href = '/user/profile/index'"><?= Yii::$app->user->identity->name_full ?></button>
                    <?php
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

    <div class="container">
        <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
