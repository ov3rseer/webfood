<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @throws \Exception */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
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

<div id="tooltips" class="d-none">
    <div id="profile_popover">
        <ul class="list-group">
            <li class="list-group-item">Cras justo odio</li>
            <li class="list-group-item">Dapibus ac facilisis in</li>
            <li class="list-group-item">Morbi leo risus</li>
            <li class="list-group-item">Porta ac consectetur ac</li>
            <li class="list-group-item">Vestibulum at eros</li>
        </ul>
    </div>
</div>

<a class="wrap">
    <?php
    $logo = Yii::getAlias('@web') . '/img/logo_color.svg';

    if (Yii::$app->user->isGuest) {
        $profileItems = [
            ['label' => 'Регистрация', 'url' => ['/site/signup']],
            ['label' => 'Вход', 'url' => ['/site/login']],
        ];
    } else {
        $profileItems = [
            ['label' => 'Мой профиль: ' . Yii::$app->user->identity->name_full, 'url' => ['/user/profile/index']],
            ['label' => 'Выход', 'url' => ['/site/logout']],
        ];
    }
    $profileList = '<div class="list-group">';
    foreach ($profileItems as $item) {
        $profileList .= '<a class="list-group-item list-group-item-action" href="'.$item['url'][0].'">'.$item['label'].'</a>';
    }
    $profileList .= '</div>';
    ?>

<nav class="navbar navbar-expand navbar-light bg-light sticky-top shadow" id="navbar">
    <a class="h1 m-0" data-popover-target="#profile_popover" data-container="body" data-toggle="popover" data-placement="bottom" data-content="<?= $profileList ?>"><i class="fas fa-user-circle pointer"></i></a>
    <?php
    $menuItems = [
        //['label' => "", 'options' => ['class' => 'h1 m-0'], 'items' => $profileItems],
        ['label' => 'Главная', 'url' => ['/site/index']],
        ['label' => 'Техподдержка', 'url' => ['/site/contact']],
    ];
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav', 'id' => 'navbar_menu'],
        'items' => $menuItems,
    ]);
    ?>
</nav>

<div class="container-fluid">
<?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
<?= Alert::widget() ?>
<?= $content ?>
</div>
</div>
</a>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
