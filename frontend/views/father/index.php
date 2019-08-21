<?php

/* @var $this yii\web\View */

$this->title = 'WebFood';

use common\models\enum\UserType;
use common\models\reference\Father;
use yii\bootstrap\Html;

$father = null;
if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER) {
    /** @var Father $father */
    $father = Father::findOne(['user_id' => Yii::$app->user->id]);
}

if ($father) {
    $father->fatherChildren
    ?>


        <div class="container">
            <div class="col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span style="display: flex; justify-content: space-between;">
                            <span class="panel-title h2">Ваши дети</span>
                            <?= Html::a(' <span class="glyphicon glyphicon-plus"></span><span class="glyphicon glyphicon-user"></span>', '#', ['class' => 'text-success']) ?>
                        </span>
                    </div>
                    <div class="panel-body">

                    </div>
                </div>
            </div>
        </div>


    <?php
}
?>